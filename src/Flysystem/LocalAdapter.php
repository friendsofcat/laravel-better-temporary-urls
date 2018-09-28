<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem;

use League\Flysystem\Adapter\Local as FlysystemLocalAdapter;

class LocalAdapter extends FlysystemLocalAdapter
{

    /**
     * @param $path
     * @param $ttl
     *
     * @return string
     */
    public function getTemporaryUrl($path, $ttl)
    {
        return route('local-filesystem.temporary-url', ['path' => $path]);
    }
}
