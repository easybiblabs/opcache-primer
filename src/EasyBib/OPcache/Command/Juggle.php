<?php
namespace EasyBib\OPcache\Command;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\OutputInterface;

class Juggle extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('opcache:juggle')
            ->setDescription('Prime the opcache, remove old')
            ->addArgument(
                'path-new',
                Input\InputArgument::REQUIRED,
                'Path to be primed'
            )
            ->addArgument(
                'path-old',
                Input\InputArgument::REQUIRED,
                'Path to be invalidated'
            )
            ->addArgument(
                'path-base',
                Input\InputArgument::REQUIRED,
                'Path base'
            )
            ->addArgument(
                'fastcgi-address',
                Input\InputArgument::REQUIRED,
                self::DESC_FCGI_ADDRESS
            )
            ->addArgument(
                'fastcgi-port',
                Input\InputArgument::OPTIONAL,
                self::DESC_FCGI_PORT,
                null
            )
        ;
    }

    protected function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $connection = $this->setupFastCgiConnection(
            $input->getArgument('fastcgi-address'),
            $input->getArgument('fastcgi-port')

        );

        $content = sprintf(
            'path-new=%s&path-old=%s&path-base=%s',
            $input->getArgument('path-new'),
            $input->getArgument('path-old'),
            $input->getArgument('path-base')
        );

        $request = $connection->newRequest(
            [
                'FastCGI/1.0',
                'REQUEST_METHOD' => 'POST',
                'SCRIPT_FILENAME' => __DIR__ . '/_juggle.php',
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
                'CONTENT_LENGTH' => strlen($content),
            ],
            $content
        );

        $response = $connection->request($request);
        if (!empty($response->error)) {
            throw new \RuntimeException($response->error);
        }

        $output->writeln('Success!');
    }
}
