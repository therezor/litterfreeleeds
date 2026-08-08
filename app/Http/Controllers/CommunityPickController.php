<?php

namespace App\Http\Controllers;

use App\Models\CommunityPick;
use App\Models\Postcode;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommunityPickController extends Controller
{
    /**
     * Past picks are a record, not a listing — show a recent window rather than
     * every pick ever run on one page.
     */
    private const ARCHIVE_LIMIT = 12;

    /**
     * Everything the listing renders. `description` is deliberately absent — it
     * is the one large column and only the detail page shows it.
     *
     * @var list<string>
     */
    private const INDEX_COLUMNS = [
        'id', 'name', 'slug', 'date', 'time_from', 'time_to', 'excerpt',
        'location', 'postcode', 'latitude', 'longitude',
    ];

    public function index(Request $request): View
    {
        $searchedPostcode = Postcode::normalise($request->string('postcode')->toString());
        $origin = $searchedPostcode === '' ? null : Postcode::find($searchedPostcode);

        $upcoming = CommunityPick::query()
            ->select(self::INDEX_COLUMNS)
            ->with(['ward', 'district'])
            ->upcoming()
            ->when($origin, fn (Builder $query, Postcode $origin) => $query
                ->withDistanceFrom($origin->latitude, $origin->longitude)
                // upcoming() has already ordered by date; clear that so distance
                // is the primary sort rather than a tie-breaker.
                ->reorder()
                ->orderBy('distance_miles')
                ->orderBy('date')
                ->orderBy('time_from'))
            ->get();

        $archive = CommunityPick::query()
            ->select(self::INDEX_COLUMNS)
            ->with(['ward', 'district'])
            ->past()
            ->limit(self::ARCHIVE_LIMIT)
            ->get();

        // Grouping by month only makes sense while the list is chronological.
        // Once it is sorted by distance the months interleave, so collapse it
        // into one group. groupBy preserves insertion order either way, and
        // keying on the display label avoids re-parsing a 'Y-m' string.
        $groupKey = $origin === null
            ? fn (CommunityPick $pick): string => $pick->date->translatedFormat('F Y')
            : fn (): string => 'Nearest first';

        return view('pages.upcoming-picks', [
            'monthlyPicks' => $upcoming->groupBy($groupKey),
            'archivePicks' => $archive,
            'searchedPostcode' => $searchedPostcode === '' ? null : Postcode::format($searchedPostcode),
            'searchFailed' => $searchedPostcode !== '' && $origin === null,
            'isDistanceSorted' => $origin !== null,
        ]);
    }

    public function show(CommunityPick $communityPick): View
    {
        $communityPick->load(['ward', 'district', 'responsibleUser:id,name']);

        return view('pages.upcoming-pick', ['pick' => $communityPick]);
    }
}
