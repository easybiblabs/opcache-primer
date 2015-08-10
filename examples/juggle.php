<?php
/**
 * This could be called by bin/opcache-primer.php
 */
require dirname(__DIR__) . '/vendor/autoload.php';
$prime = new \EasyBib\OPcache\Juggler('/vagrant');
$status = $prime->recycle($_POST['new'], $_POST['old']);
exit($status);

