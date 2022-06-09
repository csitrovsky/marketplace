<?php
#
include_once __DIR__ . '/init.php';

#
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

#
try {
    $api = (new \http\api\Marketplace($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']));
    echo $api->process();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}