<?php

use Aws\S3\S3Client;
use Aws\Common\Credentials\Credentials;

class S3 {

  private static $client;

  function __construct($access_key, $secret_key, $region) {
    $credentials = new Aws\Credentials\Credentials($access_key, $secret_key);

    self::$client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => $credentials
    ]);
  }

  function listBuckets() {
    $result = self::$client->listBuckets();
    return $result['Buckets'];
  }

  function listObjects($bucket) {
    $objects = self::$client->getIterator('ListObjects', array(
      'Bucket' => $bucket
    ));

    $array_result = array();

    foreach ($objects as $object)
    array_push($array_result, array("Key" => $object["Key"],
    "Size" => $object["Size"],
    "LastModified" => $object["LastModified"],
    "Url" => self::getObjectUrl($bucket, $object["Key"])));

    return $array_result;
  }

  function getObjectUrl($bucket, $keyname) {
    $cmd = self::$client->getCommand('GetObject', [
      'Bucket' => $bucket,
      'Key' => $keyname
    ]);
    $request = self::$client->createPresignedRequest($cmd, '+30 minutes');
    return (string)$request->getUri();
  }

}
