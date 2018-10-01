<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem;

use Illuminate\Support\Facades\URL;
use League\Flysystem\Adapter\Local as FlysystemLocalAdapter;

class LocalAdapter extends FlysystemLocalAdapter
{

    /**
     * @param string $path
     * @param int|\DateTimeInterface $ttl
     * @param array $options
     *
     * @return string
     */
    public function getTemporaryUrl($path, $ttl, array $options = [])
    {
        $parameters = array_merge($options, ['path' => $path]);
        return URL::temporarySignedRoute('lbtu.temporary-url', $ttl, $parameters);
    }
}
