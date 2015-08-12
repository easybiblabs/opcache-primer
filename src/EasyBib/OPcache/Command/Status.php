<?php
namespace EasyBib\OPcache\Command;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('opcache:status')
            ->setDescription('Print the status')
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
            ->addOption(
                'scripts',
                null,
                Input\InputOption::VALUE_NONE,
                'Include scripts'
            )
        ;
    }

    protected function execute(Input\InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('scripts')) {
            $withScripts = 'yes';
        } else {
            $withScripts = 'no';
        }

        $connection = $this->setupFastCgiConnection(
            $input->getArgument('fastcgi-address'),
            $input->getArgument('fastcgi-port')

        );

        /**
         * These will show up in $_POST['new'] and $_POST['old']
         */
        $content = sprintf('with-scripts=%s', $withScripts);

        $request = $connection->newRequest(
            [
                'FastCGI/1.0',
                'REQUEST_METHOD' => 'POST',
                'SCRIPT_FILENAME' => __DIR__ . '/_status.php',
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
                'CONTENT_LENGTH' => strlen($content),
            ],
            $content
        );

        $response = $connection->request($request);
        if (!empty($response->error)) {
            throw new \RuntimeException($response->error);
        }

        $output->writeln($response->content);
    }
}
