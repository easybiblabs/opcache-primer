<?php
require __DIR__ . '/__boot.php';

if ('yes' === $_POST['with-scripts']) {
    var_dump(opcache_get_status(true));
} else {
    var_dump(opcache_get_status(false));
}
