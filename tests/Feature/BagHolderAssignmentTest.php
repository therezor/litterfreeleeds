<?php

namespace Tests\Feature;

use App\Actions\AssignNearestBagHolder;
use App\Models\Postcode;
use App\Models\User;
use Tests\TestCase;

class BagHolderAssignmentTest extends TestCase
{
    /**
     * The same three Leeds locations CommunityPickSearchTest uses: from the city
     * centre it is roughly 1.5 miles to Hyde Park, 3 to Roundhay and 9 to Otley.
     */
    private function seedPostcodes(): void
    {
        Postcode::factory()->withPostcode('LS1 1UR')->at(53.7965, -1.5478)->create();
        Postcode::factory()->withPostcode('LS6 2AB')->at(53.8155, -1.5680)->create();
        Postcode::factory()->withPostcode('LS8 2LQ')->at(53.8320, -1.5010)->create();
        Postcode::factory()->withPostcode('LS21 1BG')->at(53.9050, -1.6900)->create();
    }

    private function assign(User $picker): ?User
    {
        return app(AssignNearestBagHolder::class)->execute($picker);
    }

    public function test_it_assigns_the_nearest_bag_holder(): void
    {
        $this->seedPostcodes();

        $nearest = User::factory()->bagHolder()->atPostcode('LS6 2AB')->create(['name' => 'Hyde Park Holder']);
        User::factory()->bagHolder()->atPostcode('LS8 2LQ')->create(['name' => 'Roundhay Holder']);
        User::factory()->bagHolder()->atPostcode('LS21 1BG')->create(['name' => 'Otley Holder']);

        $picker = User::factory()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertTrue($this->assign($picker)->is($nearest));
        $this->assertSame($nearest->getKey(), $picker->fresh()->assigned_bag_holder_id);
    }

    public function test_it_assigns_a_different_holder_from_a_different_origin(): void
    {
        $this->seedPostcodes();

        User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();
        $otley = User::factory()->bagHolder()->atPostcode('LS21 1BG')->create();

        $picker = User::factory()->picker()->atPostcode('LS21 1BG')->create();

        $this->assertTrue($this->assign($picker)->is($otley));
    }

    /**
     * Bag holders are created by an admin in the panel, so one can exist with no
     * postcode. A null latitude would otherwise sort to the front and win.
     */
    public function test_it_ignores_bag_holders_without_a_postcode(): void
    {
        $this->seedPostcodes();

        User::factory()->bagHolder()->create(['name' => 'No Postcode Holder']);
        $located = User::factory()->bagHolder()->atPostcode('LS21 1BG')->create();

        $picker = User::factory()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertTrue($this->assign($picker)->is($located));
    }

    public function test_it_ignores_users_who_are_not_bag_holders(): void
    {
        $this->seedPostcodes();

        // Nearer, but not a bag holder.
        User::factory()->picker()->atPostcode('LS6 2AB')->create();
        User::factory()->atPostcode('LS6 2AB')->create();

        $holder = User::factory()->bagHolder()->atPostcode('LS21 1BG')->create();
        $picker = User::factory()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertTrue($this->assign($picker)->is($holder));
    }

    public function test_it_never_matches_a_bag_holder_to_themselves(): void
    {
        $this->seedPostcodes();

        $other = User::factory()->bagHolder()->atPostcode('LS21 1BG')->create();

        // Someone who is both a bag holder and a picker, at the closest postcode.
        $both = User::factory()->bagHolder()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertTrue($this->assign($both)->is($other));
    }

    public function test_it_assigns_nobody_when_no_bag_holders_exist(): void
    {
        $this->seedPostcodes();

        $picker = User::factory()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertNull($this->assign($picker));
        $this->assertNull($picker->fresh()->assigned_bag_holder_id);
    }

    public function test_it_assigns_nobody_when_the_volunteer_has_no_postcode(): void
    {
        $this->seedPostcodes();
        User::factory()->bagHolder()->atPostcode('LS6 2AB')->create();

        $picker = User::factory()->picker()->create();

        $this->assertNull($this->assign($picker));
    }

    /**
     * However far away the nearest holder is, someone always gets the volunteer —
     * there is deliberately no distance cap and no unassigned state.
     */
    public function test_it_assigns_a_very_distant_holder_rather_than_nobody(): void
    {
        Postcode::factory()->withPostcode('LS1 1UR')->at(53.7965, -1.5478)->create();
        Postcode::factory()->withPostcode('EC1A 1BB')->at(51.5200, -0.0980)->create();

        $london = User::factory()->bagHolder()->atPostcode('EC1A 1BB')->create();
        $picker = User::factory()->picker()->atPostcode('LS1 1UR')->create();

        $this->assertTrue($this->assign($picker)->is($london));
    }
}
