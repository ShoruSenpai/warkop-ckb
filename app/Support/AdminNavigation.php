<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminNavigation
{
    /**
     * Get navigation groups based on user role.
     */
    public static function visibleGroups(?string $role): array
    {
        $groups = config("admin-navigation.groups", []);
        $visibleGroups = [];

        foreach ($groups as $group) {
            $visibleItems = [];

            foreach ($group["items"] ?? [] as $item) {
                if (!self::canAccess($item, $role)) {
                    continue;
                }

                if (isset($item["children"])) {
                    $children = array_filter(
                        $item["children"],
                        fn($child) => self::canAccess($child, $role),
                    );

                    $item["children"] = array_values($children);

                    if (empty($item["children"])) {
                        continue;
                    }
                }

                $visibleItems[] = $item;
            }

            if (!empty($visibleItems)) {
                $group["items"] = $visibleItems;
                $visibleGroups[] = $group;
            }
        }

        return $visibleGroups;
    }

    /**
     * Check user role permission.
     */
    private static function canAccess(array $item, ?string $role): bool
    {
        if (!isset($item["roles"])) {
            return true;
        }

        return in_array($role, $item["roles"], true);
    }

    /**
     * Check whether the current route matches the menu.
     */
    public static function isActive(array $item): bool
    {
        $patterns = $item["active"] ?? ($item["route"] ?? []);

        if (is_string($patterns)) {
            $patterns = [$patterns];
        }

        return !empty($patterns) && request()->routeIs(...$patterns);
    }

    /**
     * Get menu URL safely.
     */
    public static function url(array $item): ?string
    {
        $routeName = $item["route"] ?? null;

        if (!$routeName || !Route::has($routeName)) {
            return null;
        }

        return route($routeName);
    }

    /**
     * Generate breadcrumbs from navigation configuration.
     */
    public static function breadcrumbs(
        ?string $routeName,
        ?string $fallback = null,
    ): array {
        $groups = config("admin-navigation.groups", []);

        foreach ($groups as $group) {
            foreach ($group["items"] ?? [] as $item) {
                // Check dropdown children first.
                if (!empty($item["children"])) {
                    foreach ($item["children"] as $child) {
                        if (self::isActive($child)) {
                            $pageTitle =
                                $item["pages"][$routeName ?? ""] ??
                                ($child["pages"][$routeName ?? ""] ??
                                    $child["label"]);

                            return [$item["label"], $pageTitle];
                        }
                    }

                    continue;
                }

                // Check normal menu.
                if (self::isActive($item)) {
                    $pageTitle =
                        $item["pages"][$routeName ?? ""] ?? $item["label"];

                    return [$pageTitle];
                }
            }
        }

        return [$fallback ?? "Admin Panel"];
    }
}
