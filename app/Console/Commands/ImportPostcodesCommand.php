<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Postcode;
use App\Models\Ward;
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Imports postcode centroids from a locally downloaded ONS Postcode Directory.
 *
 * Download a release from https://geoportal.statistics.gov.uk/search?tags=onspd
 * and extract it into storage/app/onspd — the download URL is versioned per
 * quarterly release, so the command reads from disk rather than fetching
 * anything itself.
 *
 * ONSPD is published under the Open Government Licence v3.0 and contains
 * Ordnance Survey and Royal Mail data; attribution is a condition of use and
 * lives in the site footer.
 */
class ImportPostcodesCommand extends Command
{
    protected $signature = 'postcodes:import
        {path? : Path to the extracted ONSPD CSV or directory. Defaults to storage/app/onspd}
        {--only= : Comma-separated postcode prefixes to import, e.g. LS,BD,WF}
        {--fresh : Empty the postcodes table before importing}
        {--chunk=1000 : Rows per upsert}';

    protected $description = 'Import postcode centroids from a locally downloaded ONS Postcode Directory (ONSPD) CSV';

    /**
     * ONSPD reuses these four column names across releases, but the full column
     * set changes — so every lookup goes through the header row by name.
     */
    private const REQUIRED_COLUMNS = ['pcds', 'doterm', 'lat', 'long'];

    /**
     * Matched as patterns, not fixed names: ONS renamed these wholesale between
     * releases — `osward` and `oslaua` up to 2024, `wd25cd` and `lad25cd` in
     * May 2026, with the year moving every time.
     */
    private const WARD_COLUMN = '/^(osward|wd\d*cd)$/';

    private const DISTRICT_COLUMN = '/^(oslaua|lad\d*cd|ltla\d*cd)$/';

    /**
     * The lookup files that ship under Documents/, whose names carry the
     * release date. Each maps an ONS code to a human name.
     *
     * @var array<class-string, array{files: list<string>, code: string, name: string}>
     */
    private const LOOKUPS = [
        Ward::class => [
            'files' => ['WD Ward names and codes*.csv', '*Ward names and codes*.csv'],
            'code' => '/^(wd|ward)\d*cd$/',
            'name' => '/^(wd|ward)\d*nm$/',
        ],
        District::class => [
            'files' => ['LAD Local Authority District names and codes*.csv', '*LA_UA names and codes*.csv'],
            'code' => '/^(lad|ltla|la_ua)\d*cd$/',
            'name' => '/^(lad|ltla|la_ua)\d*nm$/',
        ],
    ];

    /**
     * ONSPD marks postcodes it cannot place with this latitude.
     */
    private const UNLOCATABLE_LATITUDE = 99.0;

    /**
     * Where an extracted ONSPD release is expected to live. Git-ignored — the
     * CSV is around 1GB.
     */
    private const DEFAULT_PATH = 'app/onspd';

    /**
     * Where to hunt for the Documents/ lookup files — the directory the user
     * pointed at, whatever its internal layout.
     */
    private string $searchRoot = '';

    public function handle(): int
    {
        // An accumulating query log over ~1,800 statements of 4,000 bindings
        // each is a memory bomb, and debugbar is active with APP_DEBUG=true.
        DB::connection()->disableQueryLog();

        $base = $this->argument('path') ?? storage_path(self::DEFAULT_PATH);
        $this->searchRoot = is_dir($base) ? $base : dirname($base);

        $path = $this->resolveCsvPath($base);

        if ($path === null) {
            return self::FAILURE;
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            $this->components->error("Could not open [{$path}] for reading.");

            return self::FAILURE;
        }

        $columns = $this->readHeader($handle);

        if ($columns === null) {
            fclose($handle);

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->components->info('Emptying the postcodes table.');
            Postcode::query()->truncate();
        }

        $this->importLookupNames();

        $result = $this->importRows($handle, $columns, $path);

        fclose($handle);

        $this->newLine();
        $this->components->info(sprintf(
            'Imported %s postcodes. Skipped %s terminated, %s unlocatable, %s filtered out, %s malformed.',
            number_format($result['imported']),
            number_format($result['terminated']),
            number_format($result['unlocatable']),
            number_format($result['filtered']),
            number_format($result['malformed']),
        ));

        return self::SUCCESS;
    }

    /**
     * @param  array<string, int>  $columns
     * @return array<string, int>
     */
    private function importRows(mixed $handle, array $columns, string $path): array
    {
        $only = $this->prefixFilter();
        $chunkSize = max(1, (int) $this->option('chunk'));

        $wardColumn = $this->firstMatching($columns, self::WARD_COLUMN);
        $districtColumn = $this->firstMatching($columns, self::DISTRICT_COLUMN);

        if ($wardColumn === null) {
            $this->components->warn('No ward column in this release — picks will have no ward.');
        }

        if ($districtColumn === null) {
            $this->components->warn('No local authority column in this release — picks will have no district.');
        }

        $counts = ['imported' => 0, 'terminated' => 0, 'unlocatable' => 0, 'filtered' => 0, 'malformed' => 0];
        $buffer = [];
        $read = 0;

        $this->components->info('Importing from '.$path);

        while (($row = fgetcsv($handle, escape: '')) !== false) {
            if ($row === [null]) {
                continue;
            }

            $read++;

            if (filled($row[$columns['doterm']] ?? null)) {
                $counts['terminated']++;

                continue;
            }

            $latitude = (float) ($row[$columns['lat']] ?? 0);
            $longitude = (float) ($row[$columns['long']] ?? 0);

            if ($latitude >= self::UNLOCATABLE_LATITUDE) {
                $counts['unlocatable']++;

                continue;
            }

            $postcode = Postcode::normalise($row[$columns['pcds']] ?? null);

            if (! Postcode::isWellFormed($postcode)) {
                $counts['malformed']++;

                continue;
            }

            if ($only !== [] && ! Str::startsWith($postcode, $only)) {
                $counts['filtered']++;

                continue;
            }

            $buffer[] = [
                'postcode' => $postcode,
                'outward_code' => Postcode::outwardCodeFor($postcode),
                'ward_code' => $this->codeAt($row, $wardColumn),
                'district_code' => $this->codeAt($row, $districtColumn),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
            $counts['imported']++;

            if (count($buffer) >= $chunkSize) {
                $this->flush($buffer);
                $buffer = [];
            }

            // The total is unknown without a wasteful pre-pass over a 1GB file,
            // so report progress rather than draw a bar.
            if ($read % 100_000 === 0) {
                $this->components->twoColumnDetail(
                    number_format($read).' rows read',
                    number_format($counts['imported']).' imported'
                );
            }
        }

        $this->flush($buffer);

        return $counts;
    }

    /**
     * @param  list<string>  $row
     */
    private function codeAt(array $row, ?int $column): ?string
    {
        if ($column === null) {
            return null;
        }

        $code = trim((string) ($row[$column] ?? ''));

        return $code === '' ? null : $code;
    }

    /**
     * ONSPD ships ONS *codes* in the postcode file and the human *names* in
     * separate lookups under Documents/. Those lookups are what let the site say
     * "Headingley and Hyde Park" instead of "E05011389", so import them when
     * they are there — and carry on quietly when they are not.
     */
    private function importLookupNames(): void
    {
        foreach (self::LOOKUPS as $model => $lookup) {
            $path = $this->findLookup($lookup['files']);
            $label = Str::of($model)->afterLast('\\')->lower()->plural()->toString();

            if ($path === null) {
                $this->components->warn(sprintf(
                    'No %s lookup (%s) found under %s — %s codes will import, but with no names. '
                    .'Extract the whole ONSPD zip, including its Documents/ folder.',
                    $label,
                    $lookup['files'][0],
                    $this->searchRoot,
                    $label,
                ));

                continue;
            }

            $imported = $this->importLookup($model, $path, $lookup['code'], $lookup['name']);

            if ($imported === null) {
                $this->components->warn("Could not recognise the {$label} lookup columns — skipping their names.");

                continue;
            }

            $this->components->info(number_format($imported)." {$label} imported.");
        }
    }

    /**
     * @param  class-string  $model
     */
    private function importLookup(string $model, string $path, string $codePattern, string $namePattern): ?int
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return null;
        }

        $columns = $this->headerMap($handle);
        $codeColumn = $this->firstMatching($columns, $codePattern);
        $nameColumn = $this->firstMatching($columns, $namePattern);

        if ($codeColumn === null || $nameColumn === null) {
            fclose($handle);

            return null;
        }

        $buffer = [];
        $imported = 0;

        while (($row = fgetcsv($handle, escape: '')) !== false) {
            $code = trim((string) ($row[$codeColumn] ?? ''));
            $name = trim((string) ($row[$nameColumn] ?? ''));

            if ($code === '' || $name === '') {
                continue;
            }

            $buffer[] = ['code' => $code, 'name' => $name];
            $imported++;

            if (count($buffer) >= 1000) {
                $model::query()->upsert($buffer, ['code'], ['name']);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            $model::query()->upsert($buffer, ['code'], ['name']);
        }

        fclose($handle);

        return $imported;
    }

    /**
     * Lookup filenames carry the release date — "Ward names and codes UK as at
     * 12_24.csv" — and releases move them between Documents/ and the root, so
     * search the whole tree the user pointed at rather than guessing a layout.
     */
    private function findLookup(array $patterns): ?string
    {
        if (! is_dir($this->searchRoot)) {
            return null;
        }

        $names = [];

        // CATCH_GET_CHILD so one unreadable subdirectory is stepped over rather
        // than aborting the whole import — pointing at a CSV sitting in a shared
        // directory is enough to walk into something we have no rights to.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->searchRoot, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        // The lookups are never deep, and Data/multi_csv holds 120 files we
        // have no interest in walking into.
        $files->setMaxDepth(2);

        foreach ($files as $file) {
            if ($file->isFile()) {
                $names[$file->getFilename()] = $file->getPathname();
            }
        }

        // Most specific pattern first: a release ships "WD Ward names and
        // codes" beside decoys like "WDCAS CAS ward names and codes".
        foreach ($patterns as $pattern) {
            foreach ($names as $name => $path) {
                if (fnmatch($pattern, $name, FNM_CASEFOLD)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, int>  $columns
     */
    private function firstMatching(array $columns, string $pattern): ?int
    {
        foreach ($columns as $name => $index) {
            if (preg_match($pattern, (string) $name) === 1) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $buffer
     */
    private function flush(array $buffer): void
    {
        if ($buffer === []) {
            return;
        }

        // Upsert rather than insert so re-running a quarterly release is
        // idempotent. No wrapping transaction: committing per chunk keeps the
        // WAL sane, and a partial import is fixed by running it again.
        Postcode::query()->upsert(
            $buffer,
            ['postcode'],
            ['outward_code', 'ward_code', 'district_code', 'latitude', 'longitude']
        );
    }

    /**
     * @return list<string>
     */
    private function prefixFilter(): array
    {
        return Str::of((string) $this->option('only'))
            ->explode(',')
            ->map(fn (string $prefix): string => Str::upper(trim($prefix)))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, int>|null
     */
    private function readHeader(mixed $handle): ?array
    {
        $columns = $this->headerMap($handle);

        if ($columns === []) {
            $this->components->error('The CSV appears to be empty.');

            return null;
        }

        foreach (self::REQUIRED_COLUMNS as $required) {
            if (! isset($columns[$required])) {
                $this->components->error("The ONSPD CSV is missing the required [{$required}] column.");

                return null;
            }
        }

        return $columns;
    }

    /**
     * Reads the header row into a lowercased name => index map. Everything in
     * this command looks columns up by name, never by position — the ONSPD
     * column set changes between quarterly releases.
     *
     * @return array<string, int>
     */
    private function headerMap(mixed $handle): array
    {
        $header = fgetcsv($handle, escape: '');

        if (! is_array($header)) {
            return [];
        }

        // Excel-exported ONSPD files carry a UTF-8 BOM on the first column name.
        $header[0] = str_replace("\u{FEFF}", '', (string) $header[0]);

        return array_flip(array_map(
            fn (mixed $name): string => Str::of((string) $name)->trim()->lower()->toString(),
            $header
        ));
    }

    private function resolveCsvPath(string $path): ?string
    {
        if (is_file($path)) {
            return $path;
        }

        if (! is_dir($path)) {
            $this->components->error("No such file or directory [{$path}].");

            return null;
        }

        $directory = rtrim($path, '/');

        // ONSPD ships the national file under Data/; also accept a CSV dropped
        // straight into the directory.
        $matches = array_merge(
            glob($directory.'/Data/ONSPD_*_UK.csv') ?: [],
            glob($directory.'/ONSPD_*_UK.csv') ?: [],
        );

        if (count($matches) === 1) {
            return $matches[0];
        }

        if ($matches === []) {
            $this->components->error("Could not find an ONSPD CSV inside [{$directory}].");
            $this->components->info(
                'Download a release from https://geoportal.statistics.gov.uk/search?tags=onspd '
                .'and extract it into '.storage_path(self::DEFAULT_PATH).', or pass the CSV path directly.'
            );

            return null;
        }

        $this->components->error('Found more than one ONSPD CSV. Pass the one you want directly.');

        return null;
    }
}
