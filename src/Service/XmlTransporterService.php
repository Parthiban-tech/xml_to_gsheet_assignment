<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\DataTransformer;
use App\Component\FileReader\FileReaderFactory;
use App\Interfaces\DataTransporterInterface;
use App\Interfaces\SpreadSheetInterface;
use Psr\Log\LoggerInterface;

class XmlTransporterService implements DataTransporterInterface
{

    private FileReaderFactory $fileReaderFactory;
    private SpreadSheetInterface $spreadSheet;
    private DataTransformer $dataTransformer;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        FileReaderFactory $fileReaderFactory,
        SpreadSheetInterface $spreadSheet,
        DataTransformer $dataTransformer)
    {
        $this->fileReaderFactory = $fileReaderFactory;
        $this->spreadSheet = $spreadSheet;
        $this->dataTransformer = $dataTransformer;
        $this->logger = $logger;
    }

    /**
     * @param string $file - name of the file.
     * @param string $fromSource - From where we are going to read data.
     */
    public function transport(string $file, string $fromSource)
    {
        $fileReader = $this->fileReaderFactory->getReader($fromSource);
        $generatorRecords = $fileReader->read($file);
        $recordToExport = $this->dataTransformer->transform($generatorRecords);
        $sheetId = $this->spreadSheet->export($recordToExport);
        echo "Google Sheet has been created. \n https://docs.google.com/spreadsheets/d/" . $sheetId . "\n";
        $this->logger->info("Data has been exported to spread sheet, Id: " . $sheetId);
    }

}