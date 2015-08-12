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
        ;

        $this->addStandardArguments();
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

        $this->makeRequest($connection, __DIR__ . '/_juggle.php', $content);
        $output->writeln('Success!');
    }
}
