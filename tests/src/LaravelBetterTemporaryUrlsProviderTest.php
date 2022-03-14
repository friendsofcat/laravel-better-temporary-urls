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
        $filesystemAdapter = app('filesystem')->disk('s3');

        $reflectionObject = new \ReflectionObject($filesystemAdapter);

        $adapterProperty = $reflectionObject->getProperty('adapter');
        $adapterProperty->setAccessible(true);

        $adapter = $adapterProperty->getValue($filesystemAdapter);

        $this->assertInstanceOf(AwsS3Adapter::class, $adapter);
    }

    /**
     * Instance is bound to the container.
     *
     * @covers ::boot
     */
    public function testLocalInstance()
    {
        $filesystemAdapter = $this->app->get('filesystem')->disk('local');

        $reflectionObject = new \ReflectionObject($filesystemAdapter);

        $adapterProperty = $reflectionObject->getProperty('adapter');
        $adapterProperty->setAccessible(true);

        $adapter = $adapterProperty->getValue($filesystemAdapter);

        $this->assertInstanceOf(LocalAdapter::class, $adapter);
    }
}
