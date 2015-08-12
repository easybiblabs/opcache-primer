<?php
if ('fpm-fcgi' !== PHP_SAPI) {
    die('WRONG SAPI.');
}

if ('yes' === $_POST['with-scripts']) {
    var_dump(opcache_get_status(true));
} else {
    var_dump(opcache_get_status(false));
}
