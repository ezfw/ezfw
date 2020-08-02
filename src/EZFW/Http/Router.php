<?php
namespace EZFW\Http;

class Router
{
    public const METHOD_GET = "GET";
    public const METHOD_POST = "POST";
    public const METHOD_PUT = "PUT";
    public const METHOD_PATCH = "PATCH";
    public const METHOD_DELETE = "DELETE";
    public const METHOD_OPTIONS = "OPTIONS";

    public RouteMap $routeMap;

    public function __construct()
    {
        $this->routeMap = new RouteMap('/');
    }

    public function add(string $method, string $route, $routeHandler, string $group = null) : void
    {
        $routeParts = $this->getRouteParts($route);
        $currentRouteMap = $this->routeMap;

        for ($i=0; $i < count($routeParts); $i++) {
            $current = $routeParts[$i];

            $found = false;

            foreach ($currentRouteMap->children as $child) {
                if ($child->matches($current)) {
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

            if ($currentRouteMap->matches($current) && $i == count($routeParts) - 1) {
                $currentRouteMap->methods[strtoupper($method)] = ['handler' => $routeHandler, 'group' => $group];
                break;
            }
        }
    }

    /**
     * @param Request $request
     * @return callable|null
     */
    public function resolve(Request $request)
    {
        $routeParts = $this->getRouteParts($request->path);
        $currentRouteMap = $this->routeMap;

        foreach ($routeParts as $routePart) {
            $found = false;

            foreach ($currentRouteMap->children as $child) {
                if ($child->isParameter) {
                    $request->setParameter($child->routePart, $routePart);
                }

                if ($child->matches($routePart)) {
                    $currentRouteMap = $child;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return null;
            }
        }

        return $currentRouteMap->methods[$request->method] ?? null;
    }

    /**
     * @param string $uri
     * @return String[]
     */
    protected function getRouteParts(string $uri) : array
    {
        $parts = preg_split('/\//', $uri, null, PREG_SPLIT_NO_EMPTY);

        if ($parts === false) {
            return ['/'];
        }

        return ['/', ...$parts];
    }
}
