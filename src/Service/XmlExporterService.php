<?php

declare(strict_types=1);

namespace App\Service;

use App\Utility\FileReader\FileReaderFactory;
use App\Interfaces\DataExporterInterface;
use App\Interfaces\SpreadSheetInterface;

class XmlExporterService implements DataExporterInterface
{

    private FileReaderFactory $fileReaderFactory;
    private SpreadSheetInterface $spreadSheet;
    private XmlDataTransformer $xmlDataTransformer;

    public function __construct(
        FileReaderFactory $fileReaderFactory,
        SpreadSheetInterface $spreadSheet,
        XmlDataTransformer $xmlDataTransformer)
    {
        $this->fileReaderFactory = $fileReaderFactory;
        $this->spreadSheet = $spreadSheet;
        $this->xmlDataTransformer = $xmlDataTransformer;
    }

    public function export(string $fileSourceType, string $fileName): bool
    {
        $fileReader = $this->fileReaderFactory->getReader($fileSourceType);
        $generatorDataInAssArray = $fileReader->read($fileName);
        $exportData = $this->xmlDataTransformer->transform($generatorDataInAssArray);
        $this->spreadSheet->exportToSpreadsheet($exportData);
        return true;
    }

}