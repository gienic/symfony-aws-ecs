<?php

namespace Aws\EcsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class DisplayAwsRequestCommand extends Command {

    protected function configure() {
        $this
            ->setName('ecs:get')
            ->setDescription('Display AWS Request')
            ->addArgument(
                'search',
                InputArgument::REQUIRED,
                'What is the search content?'
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'ItemSearch or ItemLookup?'
            )
            ->addOption(
                'category',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the category?'
            )
            ->addOption(
                'group',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the responseGroup?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $search = $input->getArgument('search');
        $type = $input->getOption('type');
        $category = $input->getOption('category');
        $group = $input->getOption('group');

        $style = new OutputFormatterStyle('red');
        $output->getFormatter()->setStyle('fire', $style);

        if($type == 'ItemSearch') {
            $text = sprintf('<fire>%s</fire>', 'ItemSearch ');
        } else if($type == 'ItemLookup') {
            $text = sprintf('<question>%s</question>', 'ItemLookup ');
        }

        $text .= 'with '.sprintf('<info>%s</info>', $search);
        $text .= ' in category: '.sprintf('<comment>%s</comment>', $category);
        $text .= ' and responseGroup: '.sprintf('<comment>%s</comment>', $group);

        $output->writeln($text);
    }
}
