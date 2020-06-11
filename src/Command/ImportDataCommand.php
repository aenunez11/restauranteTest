<?php

namespace Command;

use Domain\Services\Command\CommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportDataCommand extends Command
{
    /**
     * @var CommandService
     */
    private $commandService;

    private $container;

    protected static $defaultName = 'symfony:import-data';

    /**
     * TestCommand constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->commandService = $container->get(CommandService::class);
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Command to import data from json file.')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }


}
