<?php

namespace App\Http\Controllers\Apply;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apply\AuthenticateGateRequest;
use App\Models\Applicant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GateController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('applicant_id')) {
            return redirect()->route('apply.application');
        }

        return view('apply');
    }

    public function authenticate(AuthenticateGateRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $email = strtolower(trim($request->string('email')->toString()));
        $code = trim($request->string('access_code')->toString());

        $applicant = Applicant::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('access_code', $code)
            ->first();

        if (! $applicant) {
            RateLimiter::hit($this->throttleKey($request), 60);

            throw ValidationException::withMessages([
                'email' => 'We could not verify your details. Please check your registered email and access code, then try again.',
            ]);
        }

        if ($applicant->isRejected()) {
            throw ValidationException::withMessages([
                'email' => 'This application has been rejected and cannot continue.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->put('applicant_id', $applicant->id);
        $request->session()->regenerate();

        if (in_array($applicant->application_status, [
            Applicant::STATUS_INTEREST,
            Applicant::STATUS_INVITED,
            Applicant::STATUS_DOCS_RESUBMIT,
        ], true)) {
            $applicant->update(['application_status' => Applicant::STATUS_IN_PROGRESS]);
        }

        return redirect()->route('apply.application');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('applicant_id');
        $request->session()->regenerate();

        return redirect()->route('apply');
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip().'|apply-gate');
    }
}
