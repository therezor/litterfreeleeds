<?php

namespace Tests\Feature;

use App\Models\CommunityPick;
use App\Models\Postcode;
use InvalidArgumentException;
use Tests\TestCase;

class CommunityPickModelTest extends TestCase
{
    public function test_it_derives_coordinates_and_outward_code_from_the_postcode(): void
    {
        $postcode = Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        $pick = CommunityPick::factory()->atPostcode($postcode)->create();

        $this->assertSame('LS62AB', $pick->postcode);
        $this->assertSame('LS6', $pick->outward_code);
        $this->assertSame(53.8155, $pick->latitude);
        $this->assertSame(-1.5680, $pick->longitude);
    }

    public function test_it_normalises_however_the_postcode_was_typed(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $pick = CommunityPick::factory()->atPostcode('ls6  2ab')->create();

        $this->assertSame('LS62AB', $pick->postcode);
        $this->assertSame('LS6 2AB', $pick->formatted_postcode);
    }

    public function test_it_refuses_a_postcode_that_is_not_in_the_directory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CommunityPick::factory()->atPostcode('ZZ99 9ZZ')->create();
    }

    public function test_slugs_include_the_date_and_survive_a_rename(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->on('2026-09-12')->create([
            'name' => 'Roundhay Park Pick',
        ]);

        $this->assertSame('roundhay-park-pick-2026-09-12', $pick->slug);

        $pick->update(['name' => 'Roundhay Park Autumn Pick']);

        $this->assertSame('roundhay-park-pick-2026-09-12', $pick->fresh()->slug);
    }

    public function test_a_colliding_slug_gets_a_suffix(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $first = CommunityPick::factory()->atPostcode('LS6 2AB')->on('2026-09-12')->create(['name' => 'Same Pick']);
        $second = CommunityPick::factory()->atPostcode('LS6 2AB')->on('2026-09-12')->create(['name' => 'Same Pick']);

        $this->assertSame('same-pick-2026-09-12', $first->slug);
        $this->assertSame('same-pick-2026-09-12-2', $second->slug);
    }

    public function test_a_pick_dated_today_is_still_upcoming(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $today = CommunityPick::factory()
            ->atPostcode('LS6 2AB')
            ->on(CommunityPick::todayInLeeds())
            ->create();

        $this->assertTrue(CommunityPick::query()->upcoming()->whereKey($today)->exists());
        $this->assertFalse(CommunityPick::query()->past()->whereKey($today)->exists());
    }

    public function test_times_round_trip_as_wall_clock_strings(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create([
            'time_from' => '09:30',
            'time_to' => '11:45',
        ]);

        $pick = $pick->fresh();

        $this->assertSame('09:30:00', $pick->time_from);
        $this->assertSame('11:45:00', $pick->time_to);
        $this->assertSame('09:30 – 11:45', $pick->time_range);
    }
}
