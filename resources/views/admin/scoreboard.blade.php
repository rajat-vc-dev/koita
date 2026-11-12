@extends('admin.layouts.app')

@section('title', 'Scoreboard')
@section('page_title', 'Scoreboard')
@section('breadcrumb', 'Scoreboard')

@section('content')
    <div class="card mt-4">
        <div class="card-body">
            <h4>People’s Choice Awards results</h4>
            <p class="mt-2 mb-2">{{ number_format($ballotCount) }} submitted {{ Str::plural('ballot', $ballotCount) }}</p>
            <p class="text-muted mb-3">First choice earns 5 points, down to 1 point for fifth choice. Ties favor more first choices, then more second choices, and so on.</p>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Rank</th>
                            <th scope="col">Organization</th>
                            @foreach ($weights as $choice => $weight)
                                <th scope="col" class="text-right">Choice {{ $choice }} <span class="d-block text-muted">({{ $weight }} {{ Str::plural('point', $weight) }})</span></th>
                            @endforeach
                            <th scope="col" class="text-right">Total votes</th>
                            <th scope="col" class="text-right">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($results as $result)
                            <tr>
                                <td>{{ $result['rank'] }}</td>
                                <th scope="row">{{ $result['title'] }}</th>
                                @foreach ($result['counts'] as $count)
                                    <td class="text-right">{{ number_format($count) }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($result['total']) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($result['score']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($weights) + 4 }}" class="text-center text-muted">No shortlisted organizations or votes yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <section class="card mt-4 mb-4" id="submitted-ballots" aria-labelledby="submitted-ballots-title">
        <div class="card-body">
            <h4 id="submitted-ballots-title" class="mb-3">Submitted ballots</h4>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Voter name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Marketing checkbox</th>
                            <th scope="col">Ranked choices</th>
                            <th scope="col">Submitted at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ballots as $ballot)
                            <tr>
                                <th scope="row">{{ $ballot->name }}</th>
                                <td>{{ $ballot->email }}</td>
                                <td>{{ $ballot->marketing_consented_at ? 'Checked' : 'Unchecked' }}</td>
                                <td>
                                    <ol class="mb-0 pl-3">
                                        @foreach ($rankedChoices->get($ballot->id, []) as $choice)
                                            <li value="{{ $choice->rank }}">{{ $choice->profile_title }}</li>
                                        @endforeach
                                    </ol>
                                </td>
                                <td class="text-nowrap">{{ $ballot->ballot_submitted_at->format('j M Y, g:i a T') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No submitted ballots yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($ballots->hasPages())
                <div class="mt-3">{{ $ballots->links('pagination::bootstrap-4') }}</div>
            @endif
        </div>
    </section>@endsection
