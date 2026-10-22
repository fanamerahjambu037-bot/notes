<?php

$defaults = [
    'APP_ENV' => 'production',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'CACHE_DRIVER' => 'array',
    'LOG_CHANNEL' => 'stderr',
    'SESSION_DRIVER' => 'database',
    'SESSION_LIFETIME' => '480',
    'SESSION_SECURE_COOKIE' => 'true',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
];

foreach ($defaults as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

$views = '/tmp/storage/framework/views';
if (!is_dir($views)) {
    mkdir($views, 0777, true);
}

require __DIR__ . '/../public/index.php';