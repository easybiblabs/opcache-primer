<?php
require __DIR__ . '/__boot.php';
require __DIR__ . '/__autoload.php';

$prime = new \EasyBib\OPcache\Prime($_POST['path-base']);
exit($prime->doClearVarcache());
