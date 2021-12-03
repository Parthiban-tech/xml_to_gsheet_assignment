<?php

declare(strict_types=1);

namespace App\Tests;

use App\Interfaces\FileDownloaderInterface;
use App\Utility\FileReader\FileReaderFtp;
use App\Utility\FileReader\FileReaderLocal;
use PHPUnit\Framework\TestCase;

class FileReaderFtpTest extends TestCase
{
    //private FileReaderLocal $fileReaderLocal;
    private FileReaderFtp $fileReaderFtp;

    private string $fileName;
    private string $resourceDir = __DIR__ . '/data/';

    protected function setUp(): void
    {
        parent::setUp();
        $fileDownloader = $this->createMock(FileDownloaderInterface::class);
        //$this->fileReaderLocal = $this->createMock(FileReaderLocal::class);

        $this->fileName = 'employee_test.xml';

        $fileReaderLocal = new FileReaderLocal($this->resourceDir);
        $this->fileReaderFtp = new FileReaderFtp(
            $fileReaderLocal,
            $fileDownloader
        );
    }

    /** @test */
    public function it_read_file_from_ftp(){
        $generatorValues = $this->fileReaderFtp->read($this->fileName);

        $this->assertSame($this->getXmlDataByIndex(0), $generatorValues->current());
        $generatorValues->next();
        $this->assertSame($this->getXmlDataByIndex(1), $generatorValues->current());
        $generatorValues->next();
        $this->assertSame($this->getXmlDataByIndex(2), $generatorValues->current());

    }

    private function getXmlDataByIndex(int $index): array {
        $fileContent = file_get_contents($this->resourceDir . $this->fileName);
        $arrayData = (array) simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
        return (array) array_values($arrayData)[0][$index];
    }
}