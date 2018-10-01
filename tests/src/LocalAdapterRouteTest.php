<?php

namespace FriendsOfCat\Tests\LaravelBetterTemporaryUrls;

use Carbon\Carbon;
use FriendsOfCat\LaravelBetterTemporaryUrls\Http\Controller\LocalFilesystemTemporaryUrlController;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Storage;

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

    public function testTemporaryRoute()
    {
        Carbon::setTestNow();

        Storage::shouldReceive('exists')
            ->andReturn(true);
        Storage::shouldReceive('path')
            ->andReturn(__DIR__ . '/../fixtures/a.txt');

        $expected_expires_header = Carbon::now()->addMinute()->format('D, d M Y H:i:s') . ' GMT';
        $this->get($this->getTemporarySignedTestUrl())
            ->assertSuccessful()
            ->assertHeader('Cache-Control', 'private, must-revalidate')
            ->assertHeader('Expires', $expected_expires_header);
    }

    public function testTemporaryRouteFileDoesntExist()
    {
        Storage::shouldReceive('exists')
            ->andReturn(false);

        $this->get($this->getTemporarySignedTestUrl())
            ->assertStatus(400);
    }

    public function testTemporaryRouteThrowsException()
    {
        Storage::shouldReceive('exists')
            ->andReturn(true);
        Storage::shouldReceive('path')
            ->andReturn(new \Exception('BROKEN'));

        $this->get($this->getTemporarySignedTestUrl())
            ->assertStatus(422);
    }

    /**
     * @return string
     */
    private function getTemporarySignedTestUrl()
    {
        $url_generator = $this->app->get('url');
        $expires = Carbon::now()->addMinute();
        return $url_generator->temporarySignedRoute('lbtu.temporary-url', $expires, ['path' => 'fixtures/a.txt']);
    }
}
