@extends('admin.layouts.app')

@section('title', 'Applicants')
@section('page_title', 'Registrations')
@section('breadcrumb', 'Registrations')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12 mt-3">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0"></h4>
                    <a href="{{ route('admin.applicants.export') }}" id="exportExcel" class="btn btn-success btn-sm">
                        <i class="ti-export"></i> Export Excel
                    </a>
                </div>

                <div class="row mb-3" id="applicants-filters">
                    <div class="col-md-3 mb-2">
                        <label for="filter_date_from">Date From</label>
                        <input type="date" id="filter_date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="filter_date_to">Date To</label>
                        <input type="date" id="filter_date_to" class="form-control form-control-sm">
                    </div>
                    <!--<div class="col-md-2 mb-2">
                        <label for="filter_registered">Eligible</label>
                        <select id="filter_registered" class="form-control form-control-sm">
                            <option value="all">All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>-->
                    <div class="col-md-3 mb-2">
                        <label for="filter_org_type">Organisation Type</label>
                        <select id="filter_org_type" class="form-control form-control-sm">
                            <option value="all">All</option>
                            @foreach ($organisationTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="filter_status">Status</label>
                        <select id="filter_status" class="form-control form-control-sm">
                            <option value="all">All</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" id="filter_reset" class="btn btn-light btn-sm btn-block">Reset</button>
                    </div>
                </div>

                <div class="data-tables">
                    <table id="applicants-table" class="text-center table table-hover">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>Organisation</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resubmitDocumentsModal" tabindex="-1" role="dialog" aria-labelledby="resubmitDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="resubmitDocumentsForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resubmitDocumentsModalLabel">Request document resubmit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Organisation type: <strong id="resubmitOrgType"></strong></p>
                    <p class="text-muted">Select the documents this applicant must upload again. Organisation type cannot be changed.</p>
                    <div id="resubmitDocList"></div>
                    <div class="text-danger mt-2" id="resubmitDocError">
                        @error('documents'){{ $message }}@enderror
                        @foreach ($errors->get('documents.*') as $messages)
                            @foreach ($messages as $message)
                                <div>{{ $message }}</div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="resubmitDocSend">Send resubmit email</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function () {
    function filterParams() {
        return {
            date_from: $('#filter_date_from').val(),
            date_to: $('#filter_date_to').val(),
            registered: $('#filter_registered').val(),
            organisation_type: $('#filter_org_type').val(),
            status: $('#filter_status').val()
        };
    }

    function updateExportLink() {
        var params = $.param(filterParams());
        $('#exportExcel').attr('href', '{{ route('admin.applicants.export') }}' + (params ? ('?' + params) : ''));
    }

    var table = $('#applicants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.applicants.data') }}',
            data: function (d) {
                var filters = filterParams();
                d.date_from = filters.date_from;
                d.date_to = filters.date_to;
                d.registered = filters.registered;
                d.organisation_type = filters.organisation_type;
                d.status = filters.status;
            }
        },
        order: [[5, 'desc']],
        columns: [
            { data: 'organisation_name', name: 'organisation_name' },
            { data: 'contact_person_name', name: 'contact_person_name' },
            { data: 'email', name: 'email' },
            { data: 'organisation_type_label', name: 'organisation_type', orderable: true, searchable: false },
            { data: 'status_label', name: 'application_status' },
            { data: 'registered_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#filter_date_from, #filter_date_to, #filter_registered, #filter_org_type, #filter_status').on('change', function () {
        updateExportLink();
        table.ajax.reload();
    });

    $('#filter_reset').on('click', function () {
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#filter_registered').val('all');
        $('#filter_org_type').val('all');
        $('#filter_status').val('all');
        updateExportLink();
        table.ajax.reload();
    });

    updateExportLink();

    var reopenApplicantId = @json(session('resubmit_modal_applicant'));
    var reopenDocuments = @json(old('documents', []));

    function openResubmitModal($btn, selected, keepError) {
        var docs = [];
        try {
            docs = JSON.parse($btn.attr('data-documents') || '[]');
        } catch (e) {
            docs = [];
        }

        var $list = $('#resubmitDocList').empty();
        $('#resubmitOrgType').text($btn.attr('data-org-label') || '');
        $('#resubmitDocumentsForm').attr('action', $btn.attr('data-url') || '');
        if (!keepError) {
            $('#resubmitDocError').empty();
        }
        $('#resubmitDocSend').toggle(docs.length > 0);

        if (!docs.length) {
            $list.append('<p class="text-danger mb-0">This applicant has no organisation type, so documents cannot be requested.</p>');
        }

        docs.forEach(function (doc, i) {
            var id = 'list_resubmit_doc_' + i;
            var $row = $('<div class="custom-control custom-checkbox mb-2"></div>');
            var $input = $('<input type="checkbox" class="custom-control-input" name="documents[]">')
                .attr({ id: id, value: doc.value });
            if (selected && selected.indexOf(doc.value) !== -1) {
                $input.prop('checked', true);
            }
            $row.append($input);
            $row.append($('<label class="custom-control-label"></label>').attr('for', id).text(doc.label));
            $list.append($row);
        });

        $('#resubmitDocumentsModal').modal('show');
    }

    $('#applicants-table').on('click', '.js-resubmit-docs', function () {
        openResubmitModal($(this), [], false);
    });

    $('#resubmitDocumentsForm').on('submit', function (e) {
        if ($(this).find('input[name="documents[]"]:checked').length === 0) {
            e.preventDefault();
            $('#resubmitDocError').text('Select at least one document.');
        }
    });

    table.on('draw', function () {
        if (!reopenApplicantId) {
            return;
        }
        var $btn = $('.js-resubmit-docs[data-applicant-id="' + reopenApplicantId + '"]');
        if (!$btn.length) {
            return;
        }
        openResubmitModal($btn, reopenDocuments, true);
        reopenApplicantId = null;
    });
});
</script>
@endpush
