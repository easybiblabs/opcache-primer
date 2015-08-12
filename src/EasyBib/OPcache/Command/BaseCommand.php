<?php
namespace EasyBib\OPcache\Command;

use Crunch\FastCGI\Client as FastCGI;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class BaseCommand extends SymfonyCommand
{
    const DESC_FCGI_ADDRESS = 'The fastcgi address/socket to connect to';

    const DESC_FCGI_PORT = 'Optional port';

    /**
     * @param string   $address
     * @param null|int $port
     *
     * @return \Crunch\FastCGI\Connection
     */
    protected function setupFastCgiConnection($address, $port = null)
    {
        if (substr($address, 0, 1) == '/') {
            $address = sprintf('unix://%s', $address);
        }


        $fastcgi = new FastCGI($address, $port);
        return $fastcgi->connect();
    }
}
