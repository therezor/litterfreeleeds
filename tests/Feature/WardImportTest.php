<?php

namespace Tests\Feature;

use App\Models\CommunityPick;
use App\Models\District;
use App\Models\Postcode;
use App\Models\Ward;
use Illuminate\Console\Command;
use Tests\TestCase;

/**
 * A pick's ward and district are read through its postcode, so they are always
 * whatever the current ONSPD release says and postcodes:import never touches
 * the picks table.
 */
class WardImportTest extends TestCase
{
    private const FIXTURE_DIR = __DIR__.'/../Fixtures/onspd';

    public function test_it_imports_ward_and_district_codes_onto_postcodes(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])
            ->assertExitCode(Command::SUCCESS);

        $hydePark = Postcode::find('LS62AB');

        $this->assertSame('E05011389', $hydePark->ward_code);
        $this->assertSame('E08000035', $hydePark->district_code);
    }

    public function test_it_imports_names_from_the_documents_lookups(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $this->assertSame('Headingley and Hyde Park', Ward::find('E05011389')->name);
        $this->assertSame('Leeds', District::find('E08000035')->name);
        $this->assertSame('Bradford', District::find('E08000032')->name);
    }

    public function test_it_warns_but_still_imports_when_there_are_no_lookups(): void
    {
        // A bare CSV with no Documents/ beside it — how ONSPD arrives if you
        // extract only the data file.
        $this->artisan('postcodes:import', ['path' => __DIR__.'/../Fixtures/bare/onspd-sample.csv'])
            ->expectsOutputToContain('No wards lookup')
            ->expectsOutputToContain('No districts lookup')
            ->assertExitCode(Command::SUCCESS);

        $this->assertSame(0, Ward::query()->count());
        $this->assertSame('E05011389', Postcode::find('LS62AB')->ward_code);
    }

    public function test_it_ignores_the_decoy_ward_lookups_a_release_ships(): void
    {
        // "WDCAS CAS ward names and codes" sits beside the real one; picking it
        // would silently give every postcode no ward name.
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $this->assertSame('Headingley and Hyde Park', Ward::find('E05011389')->name);
        $this->assertNull(Ward::find('00DAFA'));
    }

    public function test_it_warns_when_a_release_has_no_ward_column(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'onspd').'.csv';
        file_put_contents($path, "pcds,doterm,lat,long\nLS6 2AB,,53.8155,-1.568\n");

        $this->artisan('postcodes:import', ['path' => $path])
            ->expectsOutputToContain('No ward column')
            ->expectsOutputToContain('No local authority column')
            ->assertExitCode(Command::SUCCESS);

        $this->assertNull(Postcode::find('LS62AB')->ward_code);

        unlink($path);
    }

    public function test_a_pick_reads_its_ward_through_its_postcode(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->assertSame('Headingley and Hyde Park', $pick->ward->name);
        $this->assertSame('Leeds', $pick->district->name);
    }

    public function test_a_pick_created_before_the_import_picks_up_its_ward_afterwards(): void
    {
        // No import yet, so the seeded postcode carries no ward.
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->assertNull($pick->ward);

        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        // Nothing had to touch the picks table for this to become true.
        $this->assertSame('Headingley and Hyde Park', $pick->fresh()->ward->name);
    }

    public function test_a_pick_is_labelled_with_its_ward_and_city(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->assertSame('Headingley and Hyde Park, Leeds', $pick->place_label);
    }

    public function test_a_pick_outside_leeds_is_labelled_with_its_city(): void
    {
        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $pick = CommunityPick::factory()->atPostcode('BD1 1AA')->create();

        $this->assertSame('Bradford', $pick->district->name);
        $this->assertSame('Bradford Central, Bradford', $pick->place_label);
    }

    public function test_a_pick_with_no_ward_data_has_no_label(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->assertNull($pick->place_label);
    }

    public function test_the_import_never_writes_to_the_picks_table(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();
        $touchedAt = $pick->updated_at;

        $this->artisan('postcodes:import', ['path' => self::FIXTURE_DIR])->run();

        $this->assertTrue($touchedAt->equalTo($pick->fresh()->updated_at));
    }
}
