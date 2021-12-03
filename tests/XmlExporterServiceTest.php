<?php

declare(strict_types=1);

use App\Utility\FileReader\FileReaderFactory;
use App\Interfaces\SpreadSheetInterface;
use App\Service\XmlDataTransformer;
use App\Service\XmlExporterService;
use PHPUnit\Framework\TestCase;

class XmlExporterServiceTest extends TestCase
{

    /** @test */
    public function it_passes_input_to_other_services(){

        $fileReaderFactoryMock = $this->createMock(FileReaderFactory::class);
        $processSpreadSheetInterfaceMock = $this->createMock(SpreadSheetInterface::class);
        $xmlDataTransformerMock = $this->createMock(XmlDataTransformer::class);

        $xmlExporterService = new XmlExporterService(
            $fileReaderFactoryMock,
            $processSpreadSheetInterfaceMock,
            $xmlDataTransformerMock);

        $exportData = $xmlExporterService->export('local', 'file');
        $this->assertTrue($exportData);
    }

}