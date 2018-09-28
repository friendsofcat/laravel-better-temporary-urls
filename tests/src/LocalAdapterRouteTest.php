<?php

namespace FriendsOfCat\Tests\LaravelBetterTemporaryUrls;

use FriendsOfCat\LaravelBetterTemporaryUrls\Http\Controller\LocalFilesystemTemporaryUrlController;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature;

class LocalAdapterRouteTest extends TestCase
{

    /**
     * Ensure the local route is registered.
     */
    public function testRoutesRegistered()
    {
        $router = $this->app->get('router');

        $route = $router->getRoutes()->getByName('lbtu.temporary-url');

        $this->assertRouteFound($route);
        $this->assertRouteUsesMethod($route, Request::METHOD_GET);
        $this->assertRouteMappedToExpectedController($route, LocalFilesystemTemporaryUrlController::class);
        $this->assertRouteMappedToExpectedControllerActionMethod($route, 'handle');
        $this->assertRouteAssignedToMiddleware($route, ValidateSignature::class);
    }

}
