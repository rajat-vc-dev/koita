@extends('admin.layouts.app')

@section('title', 'Applicant #'.$applicant->id)
@section('page_title', 'Details')
@section('breadcrumb', $applicant->organisation_name)

@section('content')
<div class="row">
    <div class="col-12 mt-3">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0">Registrations</h4>
                    <div>
                        @if ($applicant->canBeInvited())
                            <form action="{{ route('admin.applicants.invite', $applicant) }}" method="POST" style="display:inline" onsubmit="return confirm('Send apply invite email?');">
                                @csrf
                                <button class="btn btn-primary btn-sm" type="submit"><i class="ti-email"></i> Send apply invite</button>
                            </form>
                        @endif
                        @if ($applicant->canRequestDocumentResubmit())
                            <button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#resubmitDocumentsModal">
                                <i class="ti-reload"></i> Request doc resubmit
                            </button>
                        @endif
                        @if ($applicant->canBeRejected())
                            <form action="{{ route('admin.applicants.reject', $applicant) }}" method="POST" style="display:inline" onsubmit="return confirm('Reject this application? The applicant will be emailed and blocked.');">
                                @csrf
                                <button class="btn btn-danger btn-sm" type="submit"><i class="ti-close"></i> Reject</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.applicants.index') }}" class="btn btn-light btn-sm">Back</a>
                    </div>
                </div>

                <div class="table-responsive registrations-details">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th width="30%">Organisation</th><td>{{ $applicant->organisation_name }}</td></tr>
                            <tr><th>Website</th><td><a href="{{ $applicant->website }}" target="_blank" rel="noopener">{{ $applicant->website }}</a></td></tr>
                            <tr><th>Contact Person</th><td>{{ $applicant->contact_person_name }} ({{ $applicant->designation }})</td></tr>
                            <tr><th>Email</th><td>{{ $applicant->email }}</td></tr>
                            <tr><th>Mobile</th><td>{{ $applicant->mobile }}</td></tr>
                            <tr><th>Organisation Type</th><td>{{ $applicant->organisationTypeLabel() }}</td></tr>
                            <tr><th>Terms Accepted</th><td>{{ $applicant->terms_accepted_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            <tr><th>Eligible (has documents)</th><td>{{ $applicant->is_registered ? 'Yes' : 'No' }}</td></tr>
                            <tr><th>Access Code</th><td>{{ $applicant->access_code ?: '—' }}</td></tr>
                            <tr><th>Status</th><td>{{ $applicant->statusLabel() }}</td></tr>
                            <tr><th>Invite Sent</th><td>{{ $applicant->invite_sent_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            <tr><th>Doc Resubmit Requested</th><td>{{ $applicant->document_resubmit_requested_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            <tr>
                                <th>Documents requested</th>
                                <td>
                                    @php $requested = $applicant->requestedResubmitFields(); @endphp
                                    @if ($applicant->document_resubmit_token && $requested !== [])
                                        {{ collect($requested)->map(fn ($field) => \App\Models\Applicant::resubmitDocumentLabel($applicant->organisation_type, $field))->join(', ') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Documents Resubmitted</th><td>{{ $applicant->documents_resubmitted_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            <tr><th>Application Submitted</th><td>{{ $applicant->application_submitted_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            <tr><th>Rejected At</th><td>{{ $applicant->rejected_at?->format('Y-m-d H:i') ?: '—' }}</td></tr>
                            @foreach ([
                                'section_8' => $applicant->organisation_type === \App\Models\Applicant::TYPE_TRUST ? 'COI' : 'Section 8 Certificate',
                                'certificate_12a' => '12A Certificate',
                                'certificate_80g' => '80G Certificate',
                                'coi' => 'COI',
                                'pan' => 'PAN',
                                'gst' => 'GST',
                            ] as $type => $label)
                                <tr>
                                    <th>{{ $label }}</th>
                                    <td>
                                        @if ($applicant->fileUrl($type))
                                            <a href="{{ $applicant->fileUrl($type) }}" target="_blank" rel="noopener">Download</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr><th>Registered At</th><td>{{ $applicant->created_at?->format('Y-m-d H:i') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body registrations-details">
                <h4 class="header-title">Step 1 - Organization details</h4>
                @if ($applicant->organizationDetail)
                    @php $org = $applicant->organizationDetail; @endphp
                    <table class="table table-bordered">
                        <tr><th width="30%">Organization Name</th><td>{{ $org->organization_name }}</td></tr>
                        <tr><th>Primary Areas</th><td>{{ implode(', ', $org->primary_areas ?? []) }}</td></tr>
                        <tr><th>Other Area</th><td>{{ $org->primary_area_other ?: '—' }}</td></tr>
                        <tr><th>Mission Overview</th><td>{!! nl2br(e($org->mission_overview)) !!}</td></tr>
                    </table>
                @else
                    <p class="text-muted mb-0">Not submitted yet.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body registrations-details">
                <h4 class="header-title">Step 2 - AI Solution Overview</h4>
                @if ($applicant->aiSolution)
                    @php $sol = $applicant->aiSolution; @endphp
                    <table class="table table-bordered">
                        <tr><th width="30%">Solution Name</th><td>{{ $sol->solution_name }}</td></tr>
                        <tr><th>Social Problem Addressed</th><td>{!! nl2br(e($sol->social_problem)) !!}</td></tr>
                        <tr><th>AI Solution</th><td>{!! nl2br(e($sol->solution_description)) !!}</td></tr>
                        <tr><th>Users of Solution</th><td>{{ implode(', ', $sol->users_of_solution ?? []) }}</td></tr>
                        <tr><th>Other Users</th><td>{{ $sol->users_other ?: '—' }}</td></tr>
                        <tr><th>Technical Details</th><td>{!! nl2br(e($sol->technical_details)) !!}</td></tr>
                        <tr><th>Solution Demo Video</th><td><a href="{{ $sol->demo_video_url }}" target="_blank" rel="noopener">{{ $sol->demo_video_url }}</a></td></tr>
                        <tr><th>User Testimonial Video</th><td>@if($sol->testimonial_video_url)<a href="{{ $sol->testimonial_video_url }}" target="_blank" rel="noopener">{{ $sol->testimonial_video_url }}</a>@else — @endif</td></tr>
                    </table>
                @else
                    <p class="text-muted mb-0">Not submitted yet.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body registrations-details">
                <h4 class="header-title">Step 3 - Impact & adoption</h4>
                @if ($applicant->impactDetail)
                    @php $impact = $applicant->impactDetail; @endphp
                    <table class="table table-bordered">
                        <tr><th width="30%">Adoption Stage</th><td>{{ $impact->adoption_stage }}</td></tr>
                        <tr><th>Number of Users</th><td>{{ $impact->user_count ?: '—' }}</td></tr>
                        <tr><th>Success Metrics</th><td>{!! nl2br(e($impact->success_measurement)) !!}</td></tr>
                        <tr><th>Outcomes / Impact</th><td>{!! nl2br(e($impact->outcomes_impact)) !!}</td></tr>
                        <tr><th>Solution PPT/PDF</th><td>@if($impact->deckUrl())<a href="{{ $impact->deckUrl() }}" target="_blank" rel="noopener">Download</a>@else — @endif</td></tr>
                        <tr><th>Brand Assets</th><td>@if($impact->brand_asset_url)<a href="{{ $impact->brand_asset_url }}" target="_blank" rel="noopener">{{ $impact->brand_asset_url }}</a>@else — @endif</td></tr>
                        <tr><th>Consent</th><td>{{ $impact->consent ? 'Yes' : 'No' }}</td></tr>
                    </table>
                @else
                    <p class="text-muted mb-0">Not submitted yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($applicant->canRequestDocumentResubmit())
    <div class="modal fade" id="resubmitDocumentsModal" tabindex="-1" role="dialog" aria-labelledby="resubmitDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.applicants.resubmit-documents', $applicant) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resubmitDocumentsModalLabel">Request document resubmit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Organisation type: <strong>{{ $applicant->organisationTypeLabel() }}</strong></p>
                        <p class="text-muted">Select the documents this applicant must upload again. Organisation type cannot be changed.</p>
                        @php
                            $docFields = array_keys(\App\Models\Applicant::documentsByType()[$applicant->organisation_type] ?? []);
                            $oldDocuments = old('documents', []);
                        @endphp
                        @forelse ($docFields as $field)
                            <div class="custom-control custom-checkbox mb-2">
                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="resubmit_doc_{{ $field }}"
                                    name="documents[]"
                                    value="{{ $field }}"
                                    @checked(is_array($oldDocuments) && in_array($field, $oldDocuments, true))
                                >
                                <label class="custom-control-label" for="resubmit_doc_{{ $field }}">
                                    {{ \App\Models\Applicant::resubmitDocumentLabel($applicant->organisation_type, $field) }}
                                </label>
                            </div>
                        @empty
                            <p class="text-danger mb-0">This applicant has no organisation type, so documents cannot be requested.</p>
                        @endforelse
                        @error('documents')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                        @foreach ($errors->get('documents.*') as $messages)
                            @foreach ($messages as $message)
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @endforeach
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        @if ($docFields !== [])
                            <button type="submit" class="btn btn-warning">Send resubmit email</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@if ($applicant->canRequestDocumentResubmit() && ($errors->has('documents') || $errors->has('documents.*')))
    @push('scripts')
        <script>
            $(function () {
                $('#resubmitDocumentsModal').modal('show');
            });
        </script>
    @endpush
@endif
