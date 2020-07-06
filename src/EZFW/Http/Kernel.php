<?php

namespace EZFW\Http;

use Exception;

class Kernel
{
    public Response $response;
    public Router $router;
    public array $beforeMiddleware = [];
    public array $afterMiddleware = [];

    public ErrorHandler $errorHandler;
    public string $errorHandlerClass = ErrorHandler::class;

    public function __construct()
    {
        $this->response = new Response();
        $this->router = new Router();
    }

    public static function boot() : Kernel
    {
        return new self();
    }

    public function handle(Request $request)
    {
        try {
            $this->callMiddleware($this->beforeMiddleware, $request);

            $routeHandler = $this->router->resolve($request);

            if (!isset($routeHandler)) {
                $this->response->notFound('not found');
            } else {
                $this->response = $this->callRouteHandler($routeHandler, $request);
            }

            $this->callMiddleware($this->afterMiddleware, $request);

            return $this->response;
        } catch (Exception $e) {
            return $this->getErrorHandler()->handle($e);
        }
    }

    public function addBeforeMiddleware($middleware)
    {
        $this->beforeMiddleware[] = $middleware;
    }

    public function addAfterMiddleware($middleware)
    {
        $this->afterMiddleware[] = $middleware;
    }

    protected function callMiddleware(array $middlewares, Request $request)
    {
        foreach ($middlewares as $middleware) {
            $callNext = false;
            $next = function (Request $request, Response $response) use (&$callNext) {
                $this->response = $response;
                $callNext = true;
            };

            if (is_string($middleware) && class_exists($middleware)) {
                $middlewareInstance = new $middleware;
                $middlewareInstance->handle($request, $this->response, $next);
                // TODO: check if middlewareInstance actually extends Middleware
            } elseif (is_callable($middleware)) {
                $middleware($request, $this->response, $next);
            }

            // TODO: throw exception if middleware doesn't exist

            if (!$callNext) {
                break;
            }
        }
    }

    protected function callRouteHandler($callback, Request $request)
    {
        if (is_string($callback) && class_exists($callback)) {
            $actionInstance = new $callback;
            return $actionInstance->handle($request, $this->response);
            // TODO: check if actionInstance actually extends Action
        } elseif (is_callable($callback)) {
            return $callback($request, $this->response);
        }
        // TODO: throw exception if couldn't run
    }

    protected function getErrorHandler() : ErrorHandler
    {
        if (!isset($this->errorHandler)) {
            $this->errorHandler = new $this->errorHandlerClass();
        }

        return $this->errorHandler;
    }

}
