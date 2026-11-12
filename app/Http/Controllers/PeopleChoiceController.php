<?php

namespace App\Http\Controllers;

use App\Mail\PeopleChoiceOtp;
use App\Models\Applicant;
use App\Models\PeopleChoiceVoter;
use App\Models\ShortlistEntry;
use App\Rules\RecaptchaV2;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PeopleChoiceController extends Controller
{
    private const MIN_VOTES = 3;

    private const MAX_VOTES = 5;

    private const OTP_ATTEMPTS = 5;

    public function show(Request $request): Response
    {
        if ($voter = $this->verifiedVoter($request)) {
            if ($voter->ballot_submitted_at) {
                return $this->page('submitted');
            }

            return $this->page('ballot', [
                'entries' => $this->eligibleEntries()->orderBy('title')->orderBy('id')->get(),
                'minVotes' => self::MIN_VOTES,
                'maxVotes' => self::MAX_VOTES,
            ]);
        }

        $pending = $request->session()->get('people_choice.pending');

        if ($pending) {
            return $this->page('verify', ['email' => $pending['email']]);
        }

        return $this->page('register');
    }

    public function register(Request $request): RedirectResponse
    {
        if ($this->verifiedVoter($request)) {
            return to_route('people-choice.show');
        }

        $captchaRequired = app()->isProduction() || filled(config('koita.recaptcha.site_key'))
            || filled(config('koita.recaptcha.secret_key'));
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'marketing_consent' => ['nullable', 'boolean'],
            'g-recaptcha-response' => [$captchaRequired ? 'required' : 'nullable', 'string', new RecaptchaV2],
        ]);

        $email = Str::lower($data['email']);
        $rateKey = 'people-choice-otp:'.hash('sha256', $email);

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            throw ValidationException::withMessages([
                'email' => 'Too many code requests for this email. Please try again later.',
            ]);
        }

        RateLimiter::hit($rateKey, 600);

        $voter = PeopleChoiceVoter::query()->firstOrCreate(
            ['email' => $email],
            ['name' => $data['name']],
        );
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $challenge = Str::random(64);
        $voter->update([
            'otp_challenge' => $challenge,
            'otp_hash' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ]);

        try {
            Mail::to($voter->email)->send(new PeopleChoiceOtp($code));
        } catch (Throwable $exception) {
            report($exception);
            PeopleChoiceVoter::query()->whereKey($voter->id)->where('otp_challenge', $challenge)
                ->update(['otp_challenge' => null, 'otp_hash' => null, 'otp_expires_at' => null]);

            throw ValidationException::withMessages([
                'email' => 'We could not send your verification code. Please try again.',
            ]);
        }

        $request->session()->put('people_choice.pending', [
            'voter_id' => $voter->id,
            'challenge' => $challenge,
            'email' => $voter->email,
            'name' => $data['name'],
            'marketing_consent' => $request->boolean('marketing_consent'),
        ]);

        return to_route('people-choice.show');
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('people_choice.pending');

        if (! $pending) {
            return to_route('people-choice.show');
        }

        $code = $request->validate(['code' => ['required', 'string', 'regex:/^[0-9]{6}$/D']])['code'];

        $error = DB::transaction(function () use ($pending, $code): ?string {
            $voter = PeopleChoiceVoter::query()->lockForUpdate()->find($pending['voter_id']);

            if (! $voter?->otp_hash || $voter->otp_challenge !== $pending['challenge'] || ! $voter->otp_expires_at?->isFuture()) {
                return 'This code has expired. Go back to enter your email again.';
            }

            if ($voter->otp_attempts >= self::OTP_ATTEMPTS) {
                return 'Too many incorrect attempts. Go back to enter your email again.';
            }

            if (! Hash::check($code, $voter->otp_hash)) {
                $voter->increment('otp_attempts');

                return 'That code is incorrect. Check your email and try again.';
            }

            $voter->update([
                'name' => $pending['name'],
                'verified_at' => now(),
                'marketing_consented_at' => $pending['marketing_consent'] ? now() : $voter->marketing_consented_at,
                'otp_challenge' => null,
                'otp_hash' => null,
                'otp_expires_at' => null,
                'otp_attempts' => 0,
            ]);

            return null;
        });

        if ($error) {
            throw ValidationException::withMessages(['code' => $error]);
        }

        $request->session()->regenerate();
        $request->session()->forget('people_choice.pending');
        $request->session()->put('people_choice.voter_id', $pending['voter_id']);

        return to_route('people-choice.show');
    }

    public function back(Request $request): RedirectResponse
    {
        $request->session()->forget('people_choice.pending');

        return to_route('people-choice.show');
    }

    public function vote(Request $request): RedirectResponse
    {
        $voter = $this->verifiedVoter($request);

        if (! $voter || $voter->ballot_submitted_at) {
            return to_route('people-choice.show');
        }

        $data = $request->validate([
            'choices' => ['required', 'array', 'list', 'min:'.self::MIN_VOTES, 'max:'.self::MAX_VOTES],
            'choices.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);

        DB::transaction(function () use ($voter, $data): void {
            $voter = PeopleChoiceVoter::query()->lockForUpdate()->findOrFail($voter->id);

            if ($voter->ballot_submitted_at) {
                return;
            }

            $entries = $this->eligibleEntries()->whereKey($data['choices'])->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            if ($entries->count() !== count($data['choices'])) {
                throw ValidationException::withMessages([
                    'choices' => 'One or more choices are unavailable. Review the shortlist and submit again.',
                ]);
            }

            $votes = [];

            foreach ($data['choices'] as $index => $id) {
                $entry = $entries->get($id);
                $votes[] = [
                    'voter_id' => $voter->id,
                    'applicant_id' => $entry->applicant_id,
                    'profile_title' => $entry->title,
                    'rank' => $index + 1,
                ];
            }

            DB::table('people_choice_votes')->insert($votes);
            $voter->update(['ballot_submitted_at' => now()]);
        }, attempts: 3);

        return to_route('people-choice.show');
    }

    public function logo(Request $request, int $entry): StreamedResponse
    {
        abort_unless($this->verifiedVoter($request), 404);
        $profile = $this->eligibleEntries()->findOrFail($entry);
        $disk = Storage::disk((string) config('koita.uploads_disk', 'local'));
        abort_unless($profile->logo_path && $disk->exists($profile->logo_path), 404);

        return $disk->response($profile->logo_path, headers: [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /** @return Builder<ShortlistEntry> */
    private function eligibleEntries(): Builder
    {
        return ShortlistEntry::query()->where('is_enabled', true)
            ->whereHas('applicant', fn (Builder $query) => $query->where('application_status', Applicant::STATUS_SUBMITTED));
    }

    private function verifiedVoter(Request $request): ?PeopleChoiceVoter
    {
        $id = $request->session()->get('people_choice.voter_id');

        return $id ? PeopleChoiceVoter::query()->whereNotNull('verified_at')->find($id) : null;
    }

    private function page(string $view, array $data = []): Response
    {
        return response()->view('people-choice.'.$view, $data, headers: ['Cache-Control' => 'private, no-store']);
    }
}
