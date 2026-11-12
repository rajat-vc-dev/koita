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
@endsection
