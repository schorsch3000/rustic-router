<?php

namespace Schorsch3000\RusticRouter;

class Router
{
    private $routes = [];
    public function addRoute($method, $path, $handler)
    {
        // path is either a literal string, e regex, a simple variable path or a callback that takes the path and returns a boolean.
        // prefixing the string with : makes it a variable path, prefixing it with ~ makes it a regex
        $route = new Route($method, $path, $handler);
        $this->routes[] = $route;
    }

    public function run()
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $path = $_SERVER["REQUEST_URI"];
        $routesMatches = 0;
        foreach ($this->routes as $route) {
            $match = $route->matches($method, $path);
            if (!$match) {
                continue;
            }
            if (!$route->getHandler()($match, $routesMatches++)) {
                break;
            }
        }
    }
}
