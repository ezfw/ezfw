<?php

namespace EZFW\Http;

class Router
{
    public const METHOD_GET = "GET";

    public $routeMap;

    public function __construct()
    {
        $this->routeMap = new RouteMap('/');
    }

    public function add(string $method, string $route, callable $callback)
    {
        // TODO: refactor and support different methods
        $routeParts = ['/', ...preg_split('/\//', $route, null, PREG_SPLIT_NO_EMPTY)];

        $currentRouteMap = $this->routeMap;

        for ($i=0; $i < count($routeParts); $i++) { 
            $current = $routeParts[$i];

            $found = false;

            foreach ($currentRouteMap->children as $child) {
                if ($child->isParameter || $child->routePart == $current) {
                    $currentRouteMap = $child;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $temp = new RouteMap($current);
                $currentRouteMap->addChild($temp);
                $currentRouteMap = $temp;
            }

            if (($currentRouteMap->isParameter || $currentRouteMap->routePart == $current) && $i == count($routeParts) - 1) {
                $currentRouteMap->callback = $callback;
                break;
            }
        }
    }

    public function resolve(Request $request)
    {
        $routeParts = ['/', ...preg_split('/\//', $request->path, null, PREG_SPLIT_NO_EMPTY)];

        $currentRouteMap = $this->routeMap;

        for ($i=0; $i < count($routeParts); $i++) { 
            $current = $routeParts[$i];

            $found = false;

            foreach ($currentRouteMap->children as $child) {
                if ($child->isParameter) {
                    $request->setParameter($child->routePart, $current);
                }

                if ($child->isParameter || $child->routePart == $current) {
                    $currentRouteMap = $child;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return null;
            }

        }

        return $currentRouteMap->callback;
    }
}