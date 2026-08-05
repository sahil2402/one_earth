<?php

return [
    'APP_ASSET_MODE' => isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']=='admin.oneearthholidays.com' ? 'live' : 'dev' // Options: 'dev' or 'live'
];
