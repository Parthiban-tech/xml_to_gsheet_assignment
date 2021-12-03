<?php

declare(strict_types=1);

namespace App\Tests;

use App\Utility\FileReader\Exception\FileNotExistException;
use App\Utility\FileReader\FileReaderLocal;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

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
        $arrayData = $this->fileReaderLocal->read($this->fileName);
        $this->assertSame(count($arrayData), count($this->getXmlData()));
    }

    /** @test */
    public function invalid_xml_content_parsing_error(){
        $this->expectError();

        $errorFilename = 'employee_error.xml';
        $this->fileReaderLocal->read($errorFilename);
    }

    /** @test */
    public function it_checks_non_existing_file_exception(){
        $this->expectException(FileNotExistException::class);

        $invalidFileName = 'employee.xml';
        $this->fileReaderLocal->read($invalidFileName);
    }

    private function getXmlData(): SimpleXMLElement|bool {
        $fileContent = file_get_contents($this->resourceDir . $this->fileName);
        return simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
    }
}