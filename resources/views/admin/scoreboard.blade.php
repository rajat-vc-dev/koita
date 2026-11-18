@extends('admin.layouts.app')

@section('title', 'Scoreboard')
@section('page_title', 'Scoreboard')
@section('breadcrumb', 'Scoreboard')

@section('content')
    {{-- Page-scoped styles: every rule is prefixed with .scoreboard so other admin pages are not affected --}}
    <style>
        .scoreboard .sb-card {
            background: #fff;
            border: 1px solid #ece9f3;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(30, 20, 60, .04);
        }
        .scoreboard .sb-card .card-body {
            padding: 20px;
        }

        /* Header */
        .scoreboard .sb-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f1a2e;
        }
        .scoreboard .sb-count {
            margin: 4px 0 0;
            color: #4a4360;
            font-size: .85rem;
        }
        .scoreboard .sb-note {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin: 12px 0 16px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #faf9fc;
            border: 1px solid #efedf4;
            color: #6b6580;
            font-size: .8rem;
        }
        .scoreboard .sb-note svg {
            flex: 0 0 auto;
            width: 14px;
            height: 14px;
            margin-top: 2px;
        }

        /* Table shell */
        .scoreboard .sb-table-wrap {
            border: 1px solid #ece9f3;
            border-radius: 12px;
            overflow: hidden;
        }
        .scoreboard .sb-table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .scoreboard .sb-table thead th {
            background: #f6f4fa;
            color: #4a4360;
            font-size: .75rem;
            font-weight: 600;
            border-top: 0;
            border-bottom: 1px solid #e6e2ef;
            padding: 8px 10px;
            vertical-align: middle;
            white-space: nowrap;
        }
        .scoreboard .sb-table thead th .text-muted {
            font-weight: 500;
            font-size: .7rem;
            color: #9a94ab !important;
        }
        /* Compact cells so the full table fits on 1366px screens */
        .scoreboard .sb-table tbody td,
        .scoreboard .sb-table tbody th {
            padding: 8px 10px;
            border-top: 1px solid #f0eef5;
            vertical-align: middle;
            color: #2b2540;
            font-size: .82rem;
        }
        .scoreboard .sb-table tbody tr:first-child td,
        .scoreboard .sb-table tbody tr:first-child th {
            border-top: 0;
        }
        .scoreboard .sb-table tbody tr:hover td,
        .scoreboard .sb-table tbody tr:hover th {
            background: #fbfaff;
        }
        .scoreboard .sb-table .sb-empty {
            padding: 32px 14px;
        }

        /* Results table */
        .scoreboard .sb-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #efecf7;
            color: #3b3355;
            font-weight: 600;
            font-size: .78rem;
        }
        .scoreboard .sb-rank--1 { background: #fdf0c4; color: #8a6100; box-shadow: inset 0 0 0 2px #f2cf5b; }
        .scoreboard .sb-rank--2 { background: #eceef2; color: #4d5563; box-shadow: inset 0 0 0 2px #c5cad3; }
        .scoreboard .sb-rank--3 { background: #f8e3d3; color: #8a4a1c; box-shadow: inset 0 0 0 2px #e0a77d; }
        .scoreboard .sb-org {
            font-weight: 600;
            min-width: 160px;
        }
        .scoreboard .sb-zero {
            color: #c9c5d4 !important;
        }
        .scoreboard .sb-num {
            font-variant-numeric: tabular-nums;
        }
        .scoreboard .sb-score {
            display: inline-block;
            min-width: 36px;
            padding: 2px 8px;
            border-radius: 6px;
            background: #1f1a2e;
            color: #fff;
            font-weight: 700;
            text-align: center;
        }

        /* Voters table */
        .scoreboard .sb-voter {
            font-weight: 600;
            min-width: 120px;
        }
        .scoreboard .sb-email {
            color: #6b6580;
            word-break: break-all;
        }
        .scoreboard .sb-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .scoreboard .sb-pill--yes { background: #e3f6e8; color: #1f9d4a; }
        .scoreboard .sb-pill--no { background: #f0eff3; color: #6b6580; }
        .scoreboard .sb-choices {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 180px;
        }
        .scoreboard .sb-choices li {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .8rem;
        }
        /* Show the rank number from the li "value" attribute as a small badge */
        .scoreboard .sb-choices li::before {
            content: attr(value);
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #efecf7;
            color: #3b3355;
            font-size: .68rem;
            font-weight: 700;
        }
        .scoreboard .sb-date {
            color: #6b6580;
            font-size: .78rem !important;
        }
        .scoreboard .pagination {
            margin-bottom: 0;
        }
    </style>

    <div class="scoreboard">
        <div class="card sb-card mt-4">
            <div class="card-body">
                <h4 class="sb-title">Audience Choice Awards results</h4>
                <p class="sb-count">{{ number_format($ballotCount) }} submitted {{ Str::plural('vote', $ballotCount) }}</p>
                <p class="sb-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>First choice earns 5 points, down to 1 point for fifth choice. Ties favor more first choices, then more second choices, and so on.</span>
                </p>

                <div class="table-responsive sb-table-wrap">
                    <table class="table sb-table">
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
                                    <td>
                                        {{-- Top 3 ranks get gold / silver / bronze badges; tied ranks share the same style --}}
                                        <span class="sb-rank {{ in_array((int) $result['rank'], [1, 2, 3], true) ? 'sb-rank--' . (int) $result['rank'] : '' }}">{{ $result['rank'] }}</span>
                                    </td>
                                    <th scope="row" class="sb-org">{{ $result['title'] }}</th>
                                    @foreach ($result['counts'] as $count)
                                        {{-- Zero counts are faded so real votes stand out --}}
                                        <td class="text-right sb-num {{ (int) $count === 0 ? 'sb-zero' : '' }}">{{ number_format($count) }}</td>
                                    @endforeach
                                    <td class="text-right sb-num">{{ number_format($result['total']) }}</td>
                                    <td class="text-right">
                                        <span class="sb-score sb-num">{{ number_format($result['score']) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($weights) + 4 }}" class="text-center text-muted sb-empty">No shortlisted organizations or votes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <section class="card sb-card mt-4 mb-4" id="submitted-ballots" aria-labelledby="submitted-ballots-title">
            <div class="card-body">
                <h4 id="submitted-ballots-title" class="sb-title mb-3">Submitted votes</h4>
                <div class="table-responsive sb-table-wrap">
                    <table class="table sb-table">
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
                                    <th scope="row" class="sb-voter">{{ $ballot->name }}</th>
                                    <td class="sb-email">{{ $ballot->email }}</td>
                                    <td>
                                        <span class="sb-pill {{ $ballot->marketing_consented_at ? 'sb-pill--yes' : 'sb-pill--no' }}">{{ $ballot->marketing_consented_at ? 'Checked' : 'Unchecked' }}</span>
                                    </td>
                                    <td>
                                        <ol class="sb-choices">
                                            @foreach ($rankedChoices->get($ballot->id, []) as $choice)
                                                <li value="{{ $choice->rank }}">{{ $choice->profile_title }}</li>
                                            @endforeach
                                        </ol>
                                    </td>
                                    <td class="text-nowrap sb-date">{{ $ballot->ballot_submitted_at->format('j M Y, g:i a T') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted sb-empty">No submitted votes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($ballots->hasPages())
                    <div class="mt-3">{{ $ballots->links('pagination::bootstrap-4') }}</div>
                @endif
            </div>
        </section>
    </div>
@endsection
