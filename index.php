<?php

ini_set('display_errors', 1);

require 'vendor/autoload.php';
require 'S3.class.php';
require 'twig_filters.php';

$ini_array = parse_ini_file("config.ini");

if (isset($ini_array['access_key']) && isset($ini_array['access_key']) && isset($ini_array['bucket'])) {
  $access_key = $ini_array["access_key"];
  $secret_key = $ini_array["secret_key"];
  $bucket = $ini_array["bucket"];
} else {
  die("Please set access_key, secret_key and bucket in config.ini file.");
}

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

$twig->addFilter($filter_format_bytes);
$twig->addFilter($filter_table_selected);
$twig->addFilter($filter_format_datetime);

$bucket = $ini_array["bucket"];

if(isset($ini_array['region'])){
  $region = $ini_array['region'];
  $s3 = new S3($access_key, $secret_key, $region);
}else{
  $s3 = new S3($access_key, $secret_key);
}

$objects = $s3->listObjects($bucket);
echo $twig->render('index.html', array("objects" => $objects));
