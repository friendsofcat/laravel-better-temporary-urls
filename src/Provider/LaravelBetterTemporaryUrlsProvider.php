<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Provider;

use Aws\S3\S3Client;
use FriendsofCat\LaravelBetterTemporaryUrls\Flysystem\AwsS3Adapter;
use FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem\LocalAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

class LaravelBetterTemporaryUrlsProvider extends ServiceProvider
{

    /**
     * @var string
     */
    protected $namespace = 'FriendsOfCat\LaravelBetterTemporaryUrls\Http\Controller';

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->localFilesystemIsDefault()) {
            $this->registerLocalFilesystem();
        }

        $this->registerS3Filesystem();

        $this->bootRoutes();
    }

    /**
     * Define the web routes.
     */
    protected function bootRoutes()
    {
        $this->app->get('router')->group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes.php');
        });
    }

    protected function registerLocalFilesystem()
    {
        $this->app->get('filesystem')->extend('local', function ($app, $config) {
            $permissions = $config['permissions'] ?? [];
            $links = ($config['links'] ?? null) === 'skip'
                ? LocalAdapter::SKIP_LINKS
                : LocalAdapter::DISALLOW_LINKS;

            return $this->createFlysystem(
                new LocalAdapter($config['root'], LOCK_EX, $links, $permissions),
                $config
            );
        });
    }

    protected function registerS3Filesystem()
    {
        $this->app->get('filesystem')->extend('s3', function ($app, $config) {
            $s3Config = $this->formatS3Config($config);

            $root = $s3Config['root'] ?? null;

            $options = $config['options'] ?? [];

            return $this->createFlysystem(
                new AwsS3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root, $options),
                $config
            );
        });
    }

    /**
     * Format the given S3 configuration with the default options.
     *
     * @param  array  $config
     * @return array
     */
    protected function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface  $adapter
     * @param  array  $config
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);

        return new Filesystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * @return bool
     */
    protected function localFilesystemIsDefault()
    {
        return config('filesystems.default', 'local') === 'local';
    }
}
