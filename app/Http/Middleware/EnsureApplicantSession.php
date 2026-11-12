<?php

namespace App\Http\Middleware;

use App\Models\Applicant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicantSession
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $applicantId = $request->session()->get('applicant_id');

        if (! $applicantId) {
            return redirect()
                ->route('apply')
                ->withErrors(['email' => 'Please enter your email and access code to continue.']);
        }

        $applicant = Applicant::query()->find($applicantId);

        if (! $applicant || ! $applicant->access_code || $applicant->isRejected()) {
            $request->session()->forget('applicant_id');

            return redirect()
                ->route('apply')
                ->withErrors(['email' => $applicant?->isRejected()
                    ? 'This application has been rejected and cannot continue.'
                    : 'Your session is invalid. Please sign in again.']);
        }

        $request->attributes->set('applicant', $applicant);

        return $next($request);
    }
}
