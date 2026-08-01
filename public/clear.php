<?php

if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Bootstrap Laravel to see the constant
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$mode = defined('APP_ASSET_MODE') ? APP_ASSET_MODE : 'not defined';

echo "OPCache reset! APP_ASSET_MODE: " . $mode;
