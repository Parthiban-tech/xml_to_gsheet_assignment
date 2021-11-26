<?php

declare(strict_types=1);

use App\Service\XmlDataTransformer;
use PHPUnit\Framework\TestCase;
use App\Tests\data\DataProvider;

class XmlDataTransformerTest extends TestCase
{
    private string $resourceDir;
    private string $fileName;

    /** @test */
    public function it_prepares_export_data_from_xml_array(){

        $dataProvider = new DataProvider();
        $inputData = $dataProvider->inputDataToTransformer();
        $xmlDataTransformer = new XmlDataTransformer();

        $result = $xmlDataTransformer->transform($inputData);
        $this->assertEquals(array_keys($inputData[0]), $result[0]);
    }
}