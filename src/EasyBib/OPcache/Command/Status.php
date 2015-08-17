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
        ;

        $this->addStandardArguments();

        $this->addOption(
            'scripts',
            null,
            Input\InputOption::VALUE_NONE,
            'Include scripts'
        );
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

        $response = $this->makeRequest($connection, __DIR__ . '/_status.php', $content);
        $output->writeln($response->content);
    }
}
