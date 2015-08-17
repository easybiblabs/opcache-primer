<?php
if ('fpm-fcgi' !== PHP_SAPI) {
    die('WRONG SAPI.' . PHP_EOL);
}

if ('POST' !== $_SERVER['REQUEST_METHOD']) {
    die('WRONG REQUEST_METHOD.' . PHP_EOL);
}

ini_set('display_errors', 1);
