<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\PathPrefixer;

class AwsS3Adapter extends AwsS3V3Adapter
{
    /**
     * Replacement for \Illuminate\Filesystem\FilesystemAdapter::getAwsTemporaryUrl
     * to NOT make additional network requests to fetch a signed URL for an object.
     *
     * @param string $path
     * @param $expiration
     * @param array $options
     *
     * @return string
     */
    public function getTemporaryUrl($path, $expiration, array $options = [])
    {
        $bucket = config('filesystems.s3.bucket');
        $expires = $this->convertToTimestamp($expiration);

        $client = app(S3Client::class);

        $credentials = $client->getCredentials()->wait();

        $awsKeyId = $credentials->getAccessKeyId();
        $awsSecret = $credentials->getSecretKey();
        $awsRegion = $client->getConfig('signing_region');

        $prefixer = new PathPrefixer(config('filesystems.s3.root'));

        $full_path = $prefixer->prefixDirectoryPath($path);

        $amzHeaders = '';
        $amzResource = sprintf('/%s/%s', $bucket, $full_path);
        $request = sprintf("GET\n\n\n%s\n%s%s", $expires, $amzHeaders, $amzResource);

        $hmac = hash_hmac('sha1', $request, $awsSecret, true);
        $base64hmac = urlencode(base64_encode($hmac));

        if ($client->getEndpoint() === 'https://s3.amazonaws.com') {
            $baseUrl = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $awsRegion);
        } else {
            $baseUrl = sprintf('%s/%s', $client->getEndpoint(), $bucket);
        }

        return sprintf('%s/%s?AWSAccessKeyId=%s&Expires=%s&Signature=%s', $baseUrl, $full_path, $awsKeyId, $expires, $base64hmac);
    }

    /**
     * Stolen from AWS SDK SignatureV4 class.
     *
     * @param $dateValue
     * @param null $relativeTimeBase
     *
     * @return false|int|string
     */
    private function convertToTimestamp($dateValue, $relativeTimeBase = null)
    {
        if ($dateValue instanceof \DateTimeInterface) {
            $timestamp = $dateValue->getTimestamp();
        } elseif (!is_numeric($dateValue)) {
            $timestamp = strtotime($dateValue, $relativeTimeBase === null ? time() : $relativeTimeBase);
        } else {
            $timestamp = $dateValue;
        }

        return $timestamp;
    }
}
