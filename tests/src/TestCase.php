<?php

namespace FriendsOfCat\Tests\LaravelBetterTemporaryUrls;

use FriendsOfCat\LaravelBetterTemporaryUrls\Provider\LaravelBetterTemporaryUrlsProvider;
use Illuminate\Routing\Route;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{

    /**
     * Get test package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelBetterTemporaryUrlsProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }

    /**
     * Assert route mapped to expected Controller.
     *
     * @param \Illuminate\Routing\Route $route
     * @param string $class
     */
    protected function assertRouteMappedToExpectedController(Route $route, $class)
    {
        $this->assertSame($class, get_class($route->getController()));
    }

    /**
     * Assert route mapped to expected controller method.
     *
     * @param \Illuminate\Routing\Route $route
     * @param string $action_method
     */
    protected function assertRouteMappedToExpectedControllerActionMethod(Route $route, $action_method)
    {
        $this->assertSame($action_method, $route->getActionMethod());
    }

    /**
     * Assert the route was found.
     *
     * @param \Illuminate\Routing\Route $route
     */
    protected function assertRouteFound(Route $route)
    {
        $this->assertInstanceOf(Route::class, $route);
    }

    /**
     * Assert the route was found.
     *
     * @param \Illuminate\Routing\Route $route
     */
    protected function assertRouteUsesMethod(Route $route, $method)
    {
        $this->assertContains($method, $route->methods());
    }

    /**
     * Assert the route is assigned to the expected middleware
     *
     * @param Route $route
     * @param $middleware
     */
    protected function assertRouteAssignedToMiddleware(Route $route, $middleware)
    {
        collect($middleware)->each(function ($middleware) use ($route) {
            $this->assertContains($middleware, $route->middleware());
        });
    }
}
