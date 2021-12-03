<?php

declare(strict_types=1);

namespace App\Application\Command;


use App\Application\Command\Exception\DataExporterException;
use App\Application\Constant\AppConstants;
use App\Interfaces\DataExporterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DataExporter extends Command
{
    protected static $defaultName = 'app:export-data';

    private const OPTION_SOURCE = 'source';
    private const ARGUMENT_FILE = 'file';

    private LoggerInterface $logger;
    private DataExporterInterface $dataExporter;

    public function __construct(DataExporterInterface $dataExporter,
                                LoggerInterface $logger)
    {
        parent::__construct();
        $this->dataExporter = $dataExporter;
        $this->logger = $logger;
    }

    public function configure()
    {
        $this
            -> setName('export:data:spreadsheet')
            -> setDescription('Export data to a spreadsheet')
            -> addOption(
                self::OPTION_SOURCE,
                null,
                InputOption::VALUE_REQUIRED,
                'Valid options: local, ftp',
                AppConstants::OPTION_SOURCE_LOCAL
            )
            -> addArgument(
                self::ARGUMENT_FILE,
                InputOption::VALUE_REQUIRED,
                'Name of the file with extension.'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info("Initiated the service to export data to spread sheet.");

        try {
            $fileSource = $input->getOption(self::OPTION_SOURCE);
            $fileName = $input->getArgument(self::ARGUMENT_FILE);

            $this->dataExporter->export($fileSource, $fileName);
        } catch(DataExporterException $e){
            $this->logger->error($e->getMessage(), [ 'exception' => $e ]);
            return self::FAILURE;
        }

        $this->logger->info("Data exported to spread sheet successfully!");
        return self::SUCCESS;

    }
}