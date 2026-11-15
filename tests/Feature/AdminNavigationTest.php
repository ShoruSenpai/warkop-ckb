<?php

namespace Tests\Feature;

use App\Support\AdminNavigation;
use App\Models\User;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_dashboard_menu_resolves_to_the_dashboard_page(): void
    {
        $dashboard = collect(config("admin-navigation.groups"))
            ->flatMap(fn (array $group) => $group["items"])
            ->firstWhere("label", "Dashboard");

        $this->assertSame(route("dashboard.index"), AdminNavigation::url($dashboard));
    }

    public function test_authenticated_home_redirects_to_the_dashboard_page(): void
    {
        $user = new User(["username" => "admin", "role" => "admin"]);

        $this->actingAs($user)
            ->get("/")
            ->assertRedirect(route("dashboard.index"));
    }
}
