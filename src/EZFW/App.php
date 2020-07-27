<?php

namespace EZFW;

use EZFW\Http\Kernel;
use EZFW\Http\Request;
use EZFW\Http\Router;

class App
{

    private static App $instance;

    private Kernel $kernel;

    public function __construct()
    {
        $this->kernel = Kernel::boot();
    }

    public static function boot(): App
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function handle(Request $request)
    {
        $response = $this->kernel->handle($request);
        $response->send();
    }

    public function before($middleware)
    {
        $this->kernel->addBeforeMiddleware($middleware);
        return $this;
    }

    public function after($middleware)
    {
        $this->kernel->addAfterMiddleware($middleware);
        return $this;
    }

    public function use($plugin, ?array $config = null)
    {
        if (is_string($plugin) && class_exists($plugin)) {
            $pluginInstance = new $plugin();
            $pluginInstance->boot($this, $config ?? []);
        }
        return $this;
    }

    public function get(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_GET, $route, $routeHandler);
        return $this;
    }

    public function post(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_POST, $route, $routeHandler);
        return $this;
    }

    public function put(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_PUT, $route, $routeHandler);
        return $this;
    }

    public function patch(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_PATCH, $route, $routeHandler);
        return $this;
    }

    public function delete(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_DELETE, $route, $routeHandler);
        return $this;
    }

    public function options(string $route, $routeHandler)
    {
        $this->kernel->router->add(Router::METHOD_OPTIONS, $route, $routeHandler);
        return $this;
    }

    public function errorHandler(string $errorHandlerClass)
    {
        $this->kernel->errorHandlerClass = $errorHandlerClass;
        return $this;
    }
}
