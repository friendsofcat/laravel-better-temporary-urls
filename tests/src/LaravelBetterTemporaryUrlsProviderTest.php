<?php

namespace FriendsOfCat\Tests\LaravelBetterTemporaryUrls;

use FriendsofCat\LaravelBetterTemporaryUrls\Flysystem\AwsS3Adapter;
use FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem\LocalAdapter;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelBetterTemporaryUrls\Provider\LaravelBetterTemporaryUrlsProvider
 */
class LaravelBetterTemporaryUrlsProviderTest extends TestCase
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
        $app->get('config')->set('filesystems.disks.s3.region', 'eu-west-1');
    }

    /**
     * Instance is bound to the container.
     *
     * @covers ::boot
     */
    public function testS3Instance()
    {
        $manager = app('filesystem');
        $this->assertInstanceOf(AwsS3Adapter::class, $manager->disk('s3')->getAdapter());
    }

    /**
     * Instance is bound to the container.
     *
     * @covers ::boot
     */
    public function testLocalInstance()
    {
        $manager = $this->app->get('filesystem');
        $this->assertInstanceOf(LocalAdapter::class, $manager->disk('local')->getAdapter());
    }
}
