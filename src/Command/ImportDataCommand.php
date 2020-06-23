<?php

namespace Command;

use Domain\Services\Importer\SegmentImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportDataCommand extends Command
{
    protected $importer;
    protected static $defaultName = 'symfony:import-data';

    /**
     * TestCommand constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->importer = $container->get(SegmentImporter::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Command to import data from json file.')
            ->addArgument('filename', InputArgument::OPTIONAL, 'The  filename');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($filename = $input->getArgument('filename'))) {
            $output->write('<error>Filename not found</error>');
        }

        $data = json_decode(file_get_contents($filename), true);
        $this->importer->import($data);
    }


}
