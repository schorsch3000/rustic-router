<?php

namespace Schorsch3000\RusticRouter;

use function array_flip;
use function array_map;

class Route
{
    private $method;
    private $pathHandler;
    private $handler;
    const METHODS = [
        "GET",
        "POST",
        "PUT",
        "DELETE",
        "HEAD",
        "OPTIONS",
        "TRACE",
        "CONNECT",
        "PATCH",
    ];
    public function __construct($method, $pathMatcher, $handler)
    {
        if (!in_array($method, self::METHODS)) {
            throw new \Exception("Invalid method $method");
        }
        $this->method = $method;
        $this->handler = $handler;

        if (is_callable($pathMatcher)) {
            $this->pathHandler = $pathMatcher;
        } else {
            switch (substr($pathMatcher, 0, 1)) {
                case ":":
                    $pathMatcher = substr($pathMatcher, 1);
                    $pathMatcher = explode("/", $pathMatcher);
                    $this->pathHandler = function ($path) use ($pathMatcher) {
                        $path = explode("/", $path);
                        if (count($path) !== count($pathMatcher)) {
                            return false;
                        }

                        $vars = [];
                        foreach ($pathMatcher as $i => $matcher) {
                            if (
                                substr($matcher, 0, 1) === ":" &&
                                substr($matcher, -1, 1) === ":"
                            ) {
                                $vars[substr($matcher, 1, -1)] = $path[$i];
                            } else {
                                if ($matcher !== $path[$i]) {
                                    return false;
                                }
                            }
                        }


                        return $vars;
                    };
                    break;
                case "~":
                    $this->pathHandler = function ($path) use ($pathMatcher) {
                        if (
                            preg_match(substr($pathMatcher, 1), $path, $matches)
                        ) {
                            return $matches;
                        }
                        return false;
                    };
                    break;
                default:
                    $this->pathHandler = function ($path) use ($pathMatcher) {
                        return $pathMatcher === $path ? $path : false;
                    };
                    break;
            }
        }
    }

    public function matches($method, $path)
    {
        if (!$this->method === $method) {
            return false;
        }
        $pathHandler = $this->pathHandler;
        return $pathHandler($path);
    }
    public function getHandler()
    {
        return $this->handler;
    }
}
