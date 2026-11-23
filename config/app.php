<?php

$config = require __DIR__ . '/local.example.php';
if (is_file(__DIR__ . '/local.php')) {
    $config = array_replace($config, require __DIR__ . '/local.php');
}

define('APP_CONFIG', $config);
define('APP_BASE_URL', rtrim($config['base_url'], '/'));
