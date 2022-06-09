<?php
#
const INC_ROOT = __DIR__;

#
session_cache_limiter(false);
session_start();

#
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

#
if (file_exists(($filename = INC_ROOT . '/http/web/vendor/autoload.php'))) {
    include_once $filename;
}

#
include_once INC_ROOT . '/app/Autoload.php';
return (new \app\Autoload())->run();