<?php

namespace Tests\Feature;

use App\Models\CommunityPick;
use App\Models\Postcode;
use Tests\TestCase;

class CommunityPicksPageTest extends TestCase
{
    public function test_it_shows_the_placeholder_when_nothing_is_published(): void
    {
        $this->get('/upcoming-picks')
            ->assertOk()
            ->assertSee('Dates are published here soon');
    }

    public function test_it_lists_community_picks_and_hides_past_ones_from_the_listing(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $upcoming = CommunityPick::factory()->atPostcode('LS6 2AB')->create(['name' => 'Woodhouse Moor Pick']);
        $past = CommunityPick::factory()->atPostcode('LS6 2AB')->past()->create(['name' => 'Last Month Pick']);

        $response = $this->get('/upcoming-picks');

        $response->assertOk()
            ->assertSee('Woodhouse Moor Pick')
            ->assertSee($upcoming->date->translatedFormat('F Y'))
            ->assertDontSee('Dates are published here soon');

        // Past picks are not gone, they have moved into the archive section.
        $response->assertSee('Recent picks')->assertSee('Last Month Pick');
        $this->assertTrue($past->date->isPast());
    }

    public function test_the_archive_is_hidden_when_there_are_no_past_picks(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->get('/upcoming-picks')->assertDontSee('Recent picks');
    }

    public function test_it_groups_picks_by_month(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();

        $first = CommunityPick::factory()->atPostcode('LS6 2AB')->on(now('Europe/London')->addMonth()->startOfMonth()->toDateString())->create();
        $second = CommunityPick::factory()->atPostcode('LS6 2AB')->on(now('Europe/London')->addMonths(2)->startOfMonth()->toDateString())->create();

        $this->get('/upcoming-picks')
            ->assertSeeInOrder([
                $first->date->translatedFormat('F Y'),
                $first->name,
                $second->date->translatedFormat('F Y'),
                $second->name,
            ]);
    }

    public function test_the_detail_page_renders_by_slug(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create([
            'description' => 'Meet by the bandstand and we will hand out bags.',
        ]);

        $this->get('/upcoming-picks/'.$pick->slug)
            ->assertOk()
            ->assertSee($pick->name)
            ->assertSee('Meet by the bandstand and we will hand out bags.')
            ->assertSee($pick->responsibleUser->name)
            ->assertSee('LS6 2AB');
    }

    public function test_an_unknown_slug_is_a_404(): void
    {
        $this->get('/upcoming-picks/no-such-pick')->assertNotFound();
    }

    public function test_the_detail_page_falls_back_to_the_excerpt_when_there_is_no_description(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create([
            'excerpt' => 'A gentle hour along the beck.',
            'description' => null,
        ]);

        $this->get('/upcoming-picks/'.$pick->slug)
            ->assertOk()
            ->assertSee('A gentle hour along the beck.');
    }

    public function test_the_detail_page_shows_a_map_of_the_meeting_point(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.568)->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->create();

        $this->get('/upcoming-picks/'.$pick->slug)
            ->assertOk()
            ->assertSee('output=embed', escape: false)
            // Encoded into the iframe's query string, comma and all.
            ->assertSee(urlencode('53.8155,-1.568'), escape: false)
            ->assertSee('Larger map');
    }

    public function test_a_past_pick_still_has_a_reachable_detail_page(): void
    {
        Postcode::factory()->withPostcode('LS6 2AB')->create();
        $pick = CommunityPick::factory()->atPostcode('LS6 2AB')->past()->create();

        $this->get('/upcoming-picks/'.$pick->slug)->assertOk();
    }
}
