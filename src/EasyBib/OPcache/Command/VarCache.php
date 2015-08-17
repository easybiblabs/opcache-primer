<?php
namespace EasyBib\OPcache\Command;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\OutputInterface;

class VarCache extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('apcu:clear')
            ->setDescription('Clear variable cache')
        ;

        $this->addStandardArguments();
    }

    protected function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $connection = $this->setupFastCgiConnection(
            $input->getArgument('fastcgi-address'),
            $input->getArgument('fastcgi-port')

        );

        $response = $this->makeRequest($connection, __DIR__ . '/_apcu-clear.php', '');
        $output->writeln($response->content);
    }
}
