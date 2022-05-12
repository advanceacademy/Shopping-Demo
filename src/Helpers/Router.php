<?php

namespace App\Helpers;

use AltoRouter;

class Router extends AltoRouter
{
    /**
     * Map a route to a target
     *
     * @param string $method One of 4 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PUT|DELETE)
     * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like  [i:id]
     * @param mixed $target The target where this route should point to. Can be anything.
     * @param string $name Optional name of this route. Supply if you want to reverse route this url in your application.
     */
    public function map($method, $route, $target, $name = null)
    {
        $route = rtrim($route, '/');
        parent::map($method, $route, $target, $name);
    }

    /**
     * Group multiple routers using prefix and controller
     *
     * @param array $options Pass 'prefix' for prefix URL and controller for all the rest routes
     * @param array $routes List of routes, see addRoutes() for details
     */
    public function group(array $options = [], array $routes = []): void
    {
        $prefix = '/' . trim($options['prefix'] ?? '/', '/') . '/';
        $controller = $options['controller'] ?? '';

        foreach ($routes as $route) {
            $this->map($route[0] ?? 'GET', $prefix . ltrim($route[1] ?? '/', '/'), $controller . '@' . $route[2]);
        }
    }
}
