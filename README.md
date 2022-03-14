# Laravel Better Temporary URLs

[![Actions Status](https://github.com/friendsofcat/laravel-better-temporary-urls/workflows/CI/badge.svg)](https://github.com/friendsofcat/laravel-better-temporary-urls/actions)

Better Support for Temporary URLs in Laravel Filesystems

### Installation

#### Versions
| Package | Laravel |
| --- | --- |
| 1.x | 8.x |
| 2.x | 9.x |


`composer require friendsofcat/laravel-better-temporary-urls`

This packages service provider is auto discovered, so the service provider does not need to be manually added.

Configuration can be published with the `artisan vendor:publish` command.

### Adapters

The following filesystem adapters can be overridden:

- `local`: An overridden version of the local adapter to provide a temporary URL (signed) route.
- `s3`: An overridden S3 adapter to provide a custom `getTemporaryUrl` method that generates signed/timed S3 links _without_
  making additional requests to get the actual link.

These adapters are enabled by default, but can be disabled in [configuration](https://github.com/friendsofcat/laravel-better-temporary-urls/blob/master/laravel-better-temporary-urls.php).
