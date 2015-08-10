#!/usr/bin/env php
<?php
$autoloader = [
    dirname(__DIR__) . '/vendor/autoload.php', // stand-alone or in bin/
    dirname(__DIR__) . '/autoload.php', // vendor/bin
];

foreach ($autoloader as $autoload) {
    if (include $autoload) {
        break;
    }
}

$options = getopt(
    '',
    [
        'new:',
        'old:',
    ]
);

if (!is_array($options) || 2 !== count($options)) {
    die('Need: --new=/path --old=/another/path' . PHP_EOL);
}

use Crunch\FastCGI\Client as FastCGI;

$socket = '/var/run/php-fpm/www-data';

$fastcgi = new FastCGI(sprintf('unix://%s', $socket), null);
$connection = $fastcgi->connect();

/**
 * These will show up in $_POST['new'] and $_POST['old']
 */
$content = sprintf('new=%s&old=%s', $options['new'], $options['old']);

$request = $connection->newRequest(
    [
        'FastCGI/1.0',
        'REQUEST_METHOD' => 'POST',
        'SCRIPT_FILENAME' => '/vagrant/examples/juggle.php',
        'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        'CONTENT_LENGTH' => strlen($content)
    ],
    $content
);

$response = $connection->request($request);
var_dump($response->content, $response->error);
