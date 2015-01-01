<?php

namespace Aws\EcsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisplayAwsRequestCommand extends Command {

    protected function configure() {
        $this
            ->setName('display:result')
            ->setDescription('Display AWS Request')
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'What type?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln(sprintf('Type: <comment>%s</comment>!', $input->getArgument('type')));
    }
}
