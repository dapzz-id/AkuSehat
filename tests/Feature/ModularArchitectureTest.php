<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class ModularArchitectureTest extends TestCase
{
    /**
     * Test that all registered routes have valid and existing controller actions.
     */
    public function test_all_registered_routes_have_valid_controllers(): void
    {
        $routes = Route::getRoutes()->getRoutes();
        $this->assertNotEmpty($routes);

        $missingActions = [];

        foreach ($routes as $route) {
            $action = $route->getActionName();

            // Ignore Closure routes
            if ($action === 'Closure' || str_contains($action, '@closure') || str_contains($action, '{closure}')) {
                continue;
            }

            if (str_contains($action, '@')) {
                [$controller, $method] = explode('@', $action);
            } else {
                $controller = $action;
                $method = '__invoke';
            }

            if (!class_exists($controller)) {
                $missingActions[] = "Missing controller class: {$controller} for route: {$route->uri()}";
                continue;
            }

            if (!method_exists($controller, $method)) {
                $missingActions[] = "Missing method: {$method} in controller: {$controller} for route: {$route->uri()}";
            }
        }

        $this->assertEmpty($missingActions, "Found invalid route actions:\n" . implode("\n", $missingActions));
    }

    /**
     * Test that legacy app/Http/Controllers directory contains only base Controller.php
     */
    public function test_legacy_controllers_directory_is_clean(): void
    {
        $dir = app_path('Http/Controllers');
        $files = scandir($dir);
        $cleanFiles = array_values(array_filter($files, fn($f) => !in_array($f, ['.', '..'])));

        $this->assertEquals(['Controller.php'], $cleanFiles, 'Legacy app/Http/Controllers directory must only contain Controller.php');
    }
}
