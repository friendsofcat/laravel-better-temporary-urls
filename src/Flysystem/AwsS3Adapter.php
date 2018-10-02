<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Flysystem;

use League\Flysystem\AwsS3v3\AwsS3Adapter as FlysystemAwsS3Adapter;

class AwsS3Adapter extends FlysystemAwsS3Adapter
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
        $bucket = $this->getBucket();
        $expires = $this->convertToTimestamp($expiration);

        $client = $this->getClient();
        $credentials = $client->getCredentials()->wait();

        $awsKeyId = $credentials->getAccessKeyId();
        $awsSecret = $credentials->getSecretKey();
        $awsRegion = $client->getConfig('signing_region');
        $full_path = $this->applyPathPrefix($path);

        $amzHeaders = '';
        $amzResource = sprintf('/%s/%s', $bucket, $full_path);
        $request = sprintf("GET\n\n\n%s\n%s%s", $expires, $amzHeaders, $amzResource);

        $hmac = hash_hmac('sha1', $request, $awsSecret, true);
        $base64hmac = urlencode(base64_encode($hmac));

        $url = 'https://%s.s3.%s.amazonaws.com/%s?AWSAccessKeyId=%s&Expires=%s&Signature=%s';
        return sprintf($url, $bucket, $awsRegion, $full_path, $awsKeyId, $expires, $base64hmac);
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
