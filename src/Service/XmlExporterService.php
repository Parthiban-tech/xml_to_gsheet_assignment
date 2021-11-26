<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\DataExporterInterface;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\SpreadSheetInterface;

class XmlExporterService implements DataExporterInterface
{

    private FileReaderInterface $fileReader;
    private SpreadSheetInterface $spreadSheet;
    private XmlDataTransformer $xmlDataTransformer;

    public function __construct(
        FileReaderInterface $fileReader,
        SpreadSheetInterface $spreadSheet,
        XmlDataTransformer $xmlDataTransformer)
    {
        $this->fileReader = $fileReader;
        $this->spreadSheet = $spreadSheet;
        $this->xmlDataTransformer = $xmlDataTransformer;
    }

    public function export(string $fileSourceType, string $fileName): bool
    {
        $xmlDataInArray = $this->fileReader->read($fileSourceType, $fileName);
        $exportData = $this->xmlDataTransformer->transform($xmlDataInArray);
        $this->spreadSheet->exportToSpreadsheet($exportData);
        return true;
    }

}