<?php

namespace Tests\Feature;

use App\Models\Postcode;
use Illuminate\Console\Command;
use Tests\TestCase;

class ImportPostcodesCommandTest extends TestCase
{
    /**
     * Deliberately not in ONSPD's published column order — the importer maps by
     * header name because the column set changes between quarterly releases.
     */
    private const FIXTURE = __DIR__.'/../Fixtures/bare/onspd-sample.csv';

    public function test_it_imports_locatable_active_postcodes(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE])
            ->assertExitCode(Command::SUCCESS);

        $this->assertEqualsCanonicalizing(
            ['LS62AB', 'LS82LQ', 'LS211BG', 'BD11AA'],
            Postcode::query()->pluck('postcode')->all()
        );

        $hydePark = Postcode::find('LS62AB');
        $this->assertSame('LS6', $hydePark->outward_code);
        $this->assertSame(53.8155, $hydePark->latitude);
        $this->assertSame(-1.568, $hydePark->longitude);
    }

    public function test_it_skips_terminated_and_unlocatable_and_malformed_rows(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE])->run();

        // Terminated (doterm set), unlocatable (lat 99.999999) and junk.
        $this->assertNull(Postcode::find('LS11UR'));
        $this->assertNull(Postcode::find('LS999ZZ'));
        $this->assertNull(Postcode::find('NOTAPOSTCODE'));
    }

    public function test_the_only_option_filters_by_prefix(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE, '--only' => 'BD'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertSame(['BD11AA'], Postcode::query()->pluck('postcode')->all());
    }

    public function test_it_is_idempotent_and_updates_coordinates(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(0.0, 0.0)->create();

        $this->artisan('postcodes:import', ['path' => self::FIXTURE])->run();
        $this->artisan('postcodes:import', ['path' => self::FIXTURE])->run();

        $this->assertSame(4, Postcode::query()->count());
        $this->assertSame(53.8155, Postcode::find('LS62AB')->latitude);
    }

    public function test_fresh_drops_postcodes_that_are_no_longer_in_the_file(): void
    {
        Postcode::factory()->withPostcode('ZZ1 1ZZ')->create();

        $this->artisan('postcodes:import', ['path' => self::FIXTURE, '--fresh' => true])->run();

        $this->assertNull(Postcode::find('ZZ11ZZ'));
        $this->assertSame(4, Postcode::query()->count());
    }

    public function test_it_finds_the_csv_inside_a_directory(): void
    {
        $directory = sys_get_temp_dir().'/onspd-'.uniqid();
        mkdir($directory.'/Data', recursive: true);
        copy(self::FIXTURE, $directory.'/Data/ONSPD_MAY_2026_UK.csv');

        $this->artisan('postcodes:import', ['path' => $directory])
            ->assertExitCode(Command::SUCCESS);

        $this->assertSame(4, Postcode::query()->count());

        unlink($directory.'/Data/ONSPD_MAY_2026_UK.csv');
        rmdir($directory.'/Data');
        rmdir($directory);
    }

    public function test_it_defaults_to_the_onspd_storage_directory(): void
    {
        // Point storage_path() at a temporary tree rather than writing a CSV
        // into the repo's own storage/app/onspd.
        $storage = $this->fakeStorageWithOnspdCsv();

        $this->artisan('postcodes:import')->assertExitCode(Command::SUCCESS);

        $this->assertSame(4, Postcode::query()->count());

        $this->removeDirectory($storage);
    }

    public function test_it_explains_itself_when_the_default_directory_is_empty(): void
    {
        $storage = sys_get_temp_dir().'/storage-'.uniqid();
        mkdir($storage.'/app/onspd', recursive: true);
        $this->app->useStoragePath($storage);

        $this->artisan('postcodes:import')
            ->expectsOutputToContain('Could not find an ONSPD CSV')
            ->assertExitCode(Command::FAILURE);

        $this->removeDirectory($storage);
    }

    /**
     * The real storage/app/onspd directory ships with the repo, git-ignored, so
     * that a coordinator has somewhere obvious to unzip an ONSPD release.
     */
    public function test_the_default_directory_exists_in_the_repository(): void
    {
        $this->assertDirectoryExists(storage_path('app/onspd'));
        $this->assertFileExists(storage_path('app/onspd/.gitignore'));
    }

    private function fakeStorageWithOnspdCsv(): string
    {
        $storage = sys_get_temp_dir().'/storage-'.uniqid();
        mkdir($storage.'/app/onspd/Data', recursive: true);
        copy(self::FIXTURE, $storage.'/app/onspd/Data/ONSPD_MAY_2026_UK.csv');

        $this->app->useStoragePath($storage);

        return $storage;
    }

    private function removeDirectory(string $path): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }

    public function test_it_fails_clearly_when_a_required_column_is_missing(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'onspd').'.csv';
        file_put_contents($path, "lat,long,doterm\n53.1,-1.1,\n");

        $this->artisan('postcodes:import', ['path' => $path])
            ->expectsOutputToContain('missing the required [pcds] column')
            ->assertExitCode(Command::FAILURE);

        unlink($path);
    }

    public function test_it_fails_when_the_path_does_not_exist(): void
    {
        $this->artisan('postcodes:import', ['path' => '/no/such/place'])
            ->assertExitCode(Command::FAILURE);
    }
}
