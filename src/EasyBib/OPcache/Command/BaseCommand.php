<?php
namespace EasyBib\OPcache\Command;

use Crunch\FastCGI;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input;

abstract class BaseCommand extends SymfonyCommand
{
    const DESC_FCGI_ADDRESS = 'The fastcgi address/socket to connect to';

    const DESC_FCGI_PORT = 'Optional port';

    protected function addStandardArguments()
    {
        $this->addArgument(
            'fastcgi-address',
            Input\InputArgument::REQUIRED,
            self::DESC_FCGI_ADDRESS
        );

        $this->addArgument(
            'fastcgi-port',
            Input\InputArgument::OPTIONAL,
            self::DESC_FCGI_PORT,
            null
        );
    }
    /**
     * @param string   $address
     * @param null|int $port
     *
     * @return FastCGI\Connection
     */
    protected function setupFastCgiConnection($address, $port = null)
    {
        if (substr($address, 0, 1) == '/') {
            $address = sprintf('unix://%s', $address);
        }

        $fastcgi = new FastCGI\Client($address, $port);
        return $fastcgi->connect();
    }

    /**
     * @param FastCGI\Connection $connection
     * @param string $primaryScript
     * @param string $content
     *
     * @return FastCGI\Response
     * @throws \RuntimeException
     */
    protected function makeRequest(FastCGI\Connection $connection, $primaryScript, $content)
    {
        $request = $connection->newRequest(
            [
                'FastCGI/1.0',
                'REQUEST_METHOD' => 'POST',
                'SCRIPT_FILENAME' => $primaryScript,
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
                'CONTENT_LENGTH' => strlen($content),
            ],
            $content
        );

        $response = $connection->request($request);
        if (!empty($response->error)) {
            throw new \RuntimeException($response->error);
        }

        return $response;
    }
}
