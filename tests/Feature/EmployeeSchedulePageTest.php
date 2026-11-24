<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class EmployeeSchedulePageTest extends TestCase
{
    public function test_owner_sees_the_schedule_management_action(): void
    {
        $owner = new User(["username" => "owner", "role" => "owner"]);

        $this->actingAs($owner)
            ->get(route("schedule.index"))
            ->assertOk()
            ->assertSee('data-role="owner"', false)
            ->assertSee("Tambah Jadwal")
            ->assertSee('id="schedule-form"', false)
            ->assertSee('id="btn-edit-schedule"', false)
            ->assertSee('id="btn-delete-schedule"', false);
    }

    public function test_admin_sees_the_schedule_page_without_management_actions(): void
    {
        $admin = new User(["username" => "admin", "role" => "admin"]);

        $this->actingAs($admin)
            ->get(route("schedule.index"))
            ->assertOk()
            ->assertSee('data-role="admin"', false)
            ->assertDontSee("Tambah Jadwal")
            ->assertDontSee('id="schedule-form"', false)
            ->assertDontSee('id="btn-edit-schedule"', false)
            ->assertDontSee('id="btn-delete-schedule"', false);
    }

    public function test_guest_cannot_open_the_schedule_page(): void
    {
        $this->get(route("schedule.index"))->assertRedirect(route("login"));
    }
}
