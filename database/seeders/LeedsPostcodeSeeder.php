<?php

namespace Database\Seeders;

use App\Models\Postcode;
use Illuminate\Database\Seeder;

/**
 * A handful of Leeds postcodes so the app is usable straight after a fresh
 * clone — picks cannot be created without a matching row in `postcodes`, and a
 * 1.8m-row ONSPD import is not a reasonable prerequisite for `db:seed`.
 *
 * The coordinates below are approximate district centres, good enough to
 * demonstrate the distance search. Run `postcodes:import` to replace them with
 * the real ONS centroids for every UK postcode; the upsert below is keyed on
 * the postcode, so the two are safe to run in either order.
 */
class LeedsPostcodeSeeder extends Seeder
{
    /**
     * @var list<array{0: string, 1: float, 2: float}>
     */
    private const POSTCODES = [
        ['LS1 1UR', 53.7965, -1.5478],
        ['LS2 9JT', 53.8008, -1.5491],
        ['LS6 2AB', 53.8155, -1.5680],
        ['LS6 4DX', 53.8190, -1.5750],
        ['LS7 4AA', 53.8250, -1.5350],
        ['LS8 2LQ', 53.8320, -1.5010],
        ['LS9 8AG', 53.8010, -1.5100],
        ['LS10 2QW', 53.7700, -1.5350],
        ['LS11 5DR', 53.7770, -1.5620],
        ['LS12 3AB', 53.7930, -1.6000],
        ['LS13 2AA', 53.8050, -1.6450],
        ['LS14 6JD', 53.8230, -1.4550],
        ['LS15 8ZB', 53.8050, -1.4470],
        ['LS16 6AU', 53.8500, -1.6000],
        ['LS17 8FA', 53.8600, -1.5400],
        ['LS18 4DR', 53.8390, -1.6400],
        ['LS21 1BG', 53.9050, -1.6900],
        ['LS28 7DP', 53.7960, -1.6620],
    ];

    public function run(): void
    {
        $rows = array_map(fn (array $row): array => [
            'postcode' => Postcode::normalise($row[0]),
            'outward_code' => Postcode::outwardCodeFor($row[0]),
            'latitude' => $row[1],
            'longitude' => $row[2],
        ], self::POSTCODES);

        Postcode::query()->upsert($rows, ['postcode'], ['outward_code', 'latitude', 'longitude']);
    }
}
