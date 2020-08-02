<?php

use EZFW\Http\Router;

use function Tests\getRequestDouble;

it('should resolve route when route is defined', function () {
    $router = new Router();
    $callable = function () {
        $a = 123;
    };
    $router->add('GET', '/test', $callable);

    $route = $router->resolve(getRequestDouble('GET', '/test'));

    assertTrue(is_callable($route['handler']));
    assertEquals($callable, $route['handler']);
});

it('should resolve null when route is not defined', function () {
    $router = new Router();

    $cb = $router->resolve(getRequestDouble('GET', '/test'));

    assertNull($cb);
});

it('should resolve route when route has parameter', function () {
    $router = new Router();
    $callable = function () {
        $a = 123;
    };
    $router->add('GET', '/test/{id}', $callable);

    $double = getRequestDouble('GET', '/test/123');
    $double->shouldReceive('setParameter')
        ->with('id', '123');
    $route = $router->resolve($double);

    assertTrue(is_callable($route['handler']));
    assertEquals($callable, $route['handler']);
});

it('should resolve routes in order they were defined', function () {
    $router = new Router();
    $callable = function () {
        $a = 123;
    };
    $router->add('GET', '/test', $callable);
    $router->add('GET', '/{id}', function () {
    });

    $double = getRequestDouble('GET', '/test');
    $double->shouldNotReceive('setParameter');
    $route = $router->resolve($double);

    assertTrue(is_callable($route['handler']));
    assertEquals($callable, $route['handler']);
});

it('should resolve route group', function () {
    $router = new Router();
    $callable = function () {
        $a = 123;
    };
    $id = 'routeid';
    $router->add('GET', '/test/{id}', $callable, $id);

    $double = getRequestDouble('GET', '/test/123');
    $double->shouldReceive('setParameter')
        ->with('id', '123');
    $route = $router->resolve($double);

    assertEquals($id, $route['group']);
});
