<?php

declare(strict_types=1);

use App\Interfaces\FileReaderInterface;
use App\Interfaces\SpreadSheetInterface;
use App\Service\XmlDataTransformer;
use App\Service\XmlExporterService;
use PHPUnit\Framework\TestCase;

class XmlExporterServiceTest extends TestCase
{

    /** @test */
    public function it_passes_data_from_one_service_to_another(){

        $fileReaderInterfaceMock = $this->createMock(FileReaderInterface::class);
        $processSpreadSheetInterface = $this->createMock(SpreadSheetInterface::class);
        $xmlDataTransformer = $this->createMock(XmlDataTransformer::class);

        $xmlExporterService = new XmlExporterService(
            $fileReaderInterfaceMock,
            $processSpreadSheetInterface,
            $xmlDataTransformer);

        $exportData = $xmlExporterService->export('source', 'file');
        $this->assertTrue($exportData);
    }

}