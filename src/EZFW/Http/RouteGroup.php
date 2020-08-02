<?php

namespace EZFW\Http;

use EZFW\App;

class RouteGroup
{
    public App $app;
    public string $baseRoute;
    public array $routes = [];
    public array $beforeMiddleware = [];
    public array $afterMiddleware = [];
    public string $id;

    public function __construct(App $app, string $route)
    {
        $this->app = $app;
        $this->baseRoute = $route;
        $this->generateId();
    }

    public function before($middleware)
    {
        $this->beforeMiddleware[] = $middleware;
        return $this;
    }

    public function after($middleware)
    {
        $this->afterMiddleware[] = $middleware;
        return $this;
    }

    public function route(array $methods, string $route, $routeHandler)
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $route, $routeHandler);
        }
        return $this;
    }

    public function get(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_GET, $route, $routeHandler);
        return $this;
    }

    public function post(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_POST, $route, $routeHandler);
        return $this;
    }

    public function put(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_PUT, $route, $routeHandler);
        return $this;
    }

    public function patch(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_PATCH, $route, $routeHandler);
        return $this;
    }

    public function delete(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_DELETE, $route, $routeHandler);
        return $this;
    }

    public function options(string $route, $routeHandler)
    {
        $this->addRoute(Router::METHOD_OPTIONS, $route, $routeHandler);
        return $this;
    }

    public function group(string $route, $routeGroupHandler)
    {
        $this->app->group($this->getFullRoute($route), $routeGroupHandler, $this);
        return $this;
    }

    public function setBaseRoute(string $route)
    {
        $this->baseRoute = $route;
        return $this;
    }

    public function clearRoutes()
    {
        $this->routes = [];
        return $this;
    }

    public function getFullRoute(string $route)
    {
        return $this->baseRoute . $route;
    }

    public function generateId() {
        $this->id = uniqid("", true);
        return $this;
    }

    protected function addRoute(string $method, string $route, $routeHandler)
    {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$route] = $routeHandler;
    }
}
