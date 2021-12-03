<?php

declare(strict_types=1);

namespace App\Tests;

use App\Utility\FileReader\Exception\FileNotExistException;
use App\Utility\FileReader\FileReaderLocal;
use PHPUnit\Framework\TestCase;

class FileReaderLocalTest extends TestCase
{

    private string $resourceDir;
    private FileReaderLocal $fileReaderLocal;
    private string $fileName;

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->resourceDir = __DIR__ . '/data/';
        $this->fileName = 'employee_test.xml';
        
        $this->fileReaderLocal = new FileReaderLocal(
            $this->resourceDir
        );
    }
    
    /** @test */
    public function it_read_file_from_local_dir()
    {
        $generatorValues = $this->fileReaderLocal->read($this->fileName);
        $this->assertSame($this->getXmlDataByIndex(0), $generatorValues->current());
        $generatorValues->next();
        $this->assertSame($this->getXmlDataByIndex(1), $generatorValues->current());
        $generatorValues->next();
        $this->assertSame($this->getXmlDataByIndex(2), $generatorValues->current());
    }

    /** @test */
    public function invalid_xml_content_parsing_error(){
        $this->expectError();

        $errorFilename = 'employee_error.xml';
        $this->fileReaderLocal->read($errorFilename)->current();
    }

    /** @test */
    public function it_checks_non_existing_file_exception(){
        $this->expectException(FileNotExistException::class);

        $invalidFileName = 'employee.xml';
        $this->fileReaderLocal->read($invalidFileName)->current();
    }

    private function getXmlDataByIndex(int $index): array {
        $fileContent = file_get_contents($this->resourceDir . $this->fileName);
        $arrayData = (array) simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
        return (array) array_values($arrayData)[0][$index];
    }
}