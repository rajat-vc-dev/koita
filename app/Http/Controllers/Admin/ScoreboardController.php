<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortlistEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScoreboardController extends Controller
{
    public function __invoke(): View
    {
        $weights = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];
        $shortlist = ShortlistEntry::query()->pluck('title', 'applicant_id');
        $tallies = DB::table('people_choice_votes as votes')
            ->join('people_choice_voters as voters', 'voters.id', '=', 'votes.voter_id')
            ->whereNotNull('voters.ballot_submitted_at')
            ->whereBetween('votes.rank', [1, count($weights)])
            ->select('votes.applicant_id', 'votes.rank')
            ->selectRaw('CASE WHEN votes.applicant_id IS NULL THEN votes.profile_title END AS archived_title')
            ->selectRaw('MAX(votes.profile_title) AS profile_title, COUNT(*) AS total')
            ->groupBy('votes.applicant_id', 'votes.rank', 'archived_title')
            ->get();

        $emptyCounts = array_fill_keys(array_keys($weights), 0);
        $results = [];

        foreach ($shortlist as $id => $title) {
            $results[$id] = ['title' => $title, 'counts' => $emptyCounts];
        }

        foreach ($tallies as $tally) {
            $key = $tally->applicant_id ?? 'archived:'.$tally->archived_title;
            $results[$key] ??= ['title' => $tally->profile_title, 'counts' => $emptyCounts];
            $results[$key]['counts'][$tally->rank] = (int) $tally->total;
        }

        foreach ($results as &$result) {
            $result['total'] = array_sum($result['counts']);
            $result['score'] = array_sum(array_map(
                fn (int $count, int $weight): int => $count * $weight,
                $result['counts'],
                $weights,
            ));
        }
        unset($result);

        usort($results, fn (array $left, array $right): int => $right['score'] <=> $left['score']
            ?: $right['counts'] <=> $left['counts']
            ?: strcasecmp($left['title'], $right['title']));

        $last = null;
        foreach ($results as $index => &$result) {
            if ($last === null || $result['counts'] !== $last['counts']) {
                $rank = $index + 1;
            }

            $result['rank'] = $rank;
            $last = $result;
        }
        unset($result);

        $ballotCount = DB::table('people_choice_voters')->whereNotNull('ballot_submitted_at')->count();

        return view('admin.scoreboard', compact('results', 'weights', 'ballotCount'));
    }
}
