<?php

namespace FriendsOfCat\Tests\LaravelBetterTemporaryUrls;

class LocalAdapterRouteDisabledTest extends TestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Override this method to set the configuration early enough, before providers run.
        $app->get('config')->set('laravel-better-temporary-urls.adapters.local', false);
    }

    /**
     * Ensure the local route is not registered when config is disabled.
     */
    public function testRoutesNotRegisteredWhenDisabled()
    {
        $router = $this->app->get('router');
        $this->assertFalse($router->getRoutes()->hasNamedRoute('lbtu.temporary-url'));
    }
}
