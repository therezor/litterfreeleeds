<?php

namespace Tests\Feature;

use App\Models\CommunityPick;
use App\Models\Postcode;
use Tests\TestCase;

class CommunityPickSearchTest extends TestCase
{
    /**
     * Three real Leeds locations, far enough apart that the ordering cannot be
     * an artefact of rounding: from Leeds city centre it is roughly 1.5 miles
     * to Hyde Park, 3 to Roundhay and 9 to Otley.
     */
    private function seedThreePicks(): array
    {
        $hydePark = Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();
        $roundhay = Postcode::factory()->withPostcode('LS8 2LQ')->at(53.8320, -1.5010)->create();
        $otley = Postcode::factory()->withPostcode('LS21 1BG')->at(53.9050, -1.6900)->create();

        Postcode::factory()->withPostcode('LS1 1UR')->at(53.7965, -1.5478)->create();

        return [
            'hydePark' => CommunityPick::factory()->atPostcode($hydePark)->create(['name' => 'Hyde Park Pick']),
            'roundhay' => CommunityPick::factory()->atPostcode($roundhay)->create(['name' => 'Roundhay Pick']),
            'otley' => CommunityPick::factory()->atPostcode($otley)->create(['name' => 'Otley Pick']),
        ];
    }

    public function test_it_sorts_picks_nearest_first(): void
    {
        $this->seedThreePicks();

        $this->get('/upcoming-picks?postcode=LS1+1UR')
            ->assertOk()
            ->assertSeeInOrder(['Hyde Park Pick', 'Roundhay Pick', 'Otley Pick'])
            ->assertSee('Sorted by distance from')
            ->assertSee('miles away');
    }

    public function test_it_sorts_from_a_different_origin(): void
    {
        $this->seedThreePicks();

        $this->get('/upcoming-picks?postcode=LS21+1BG')
            ->assertOk()
            ->assertSeeInOrder(['Otley Pick', 'Hyde Park Pick', 'Roundhay Pick']);
    }

    public function test_it_accepts_a_postcode_typed_without_a_space(): void
    {
        $this->seedThreePicks();

        $this->get('/upcoming-picks?postcode=ls11ur')
            ->assertOk()
            ->assertSee('Sorted by distance from')
            ->assertSeeInOrder(['Hyde Park Pick', 'Roundhay Pick', 'Otley Pick']);
    }

    public function test_an_unrecognised_postcode_still_lists_every_pick(): void
    {
        $this->seedThreePicks();

        $this->get('/upcoming-picks?postcode=ZZ99+9ZZ')
            ->assertOk()
            ->assertSee("don't recognise", escape: false)
            ->assertSee('Hyde Park Pick')
            ->assertSee('Roundhay Pick')
            ->assertSee('Otley Pick')
            ->assertDontSee('miles away');
    }

    public function test_a_blank_postcode_is_ignored(): void
    {
        $this->seedThreePicks();

        $this->get('/upcoming-picks?postcode=')
            ->assertOk()
            ->assertSee('Enter a postcode to sort the picks below')
            ->assertDontSee('miles away');
    }

    public function test_the_archive_stays_in_date_order_while_searching(): void
    {
        Postcode::factory()->withPostcode('LS1 1UR')->at(53.7965, -1.5478)->create();
        $far = Postcode::factory()->withPostcode('LS21 1BG')->at(53.9050, -1.6900)->create();
        $near = Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();

        // The nearer pick is older, so a distance sort would flip this order.
        CommunityPick::factory()->atPostcode($near)->past()->on('2026-01-10')->create(['name' => 'Older Near Pick']);
        CommunityPick::factory()->atPostcode($far)->past()->on('2026-02-10')->create(['name' => 'Newer Far Pick']);

        $this->get('/upcoming-picks?postcode=LS1+1UR')
            ->assertOk()
            ->assertSeeInOrder(['Newer Far Pick', 'Older Near Pick']);
    }
}
