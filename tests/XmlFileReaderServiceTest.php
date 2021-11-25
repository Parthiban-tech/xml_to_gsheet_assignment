<?php

declare(strict_types=1);

use App\Application\Constant\AppConstants;
use App\Interfaces\IFTPAdapter;
use App\Service\Exception\FileNotExistException;
use App\Service\Exception\FTPServerConnectionRefusedException;
use App\Service\XmlFileReaderService;
use PHPUnit\Framework\TestCase;

class XmlFileReaderServiceTest extends TestCase
{
    private IFTPAdapter $ftpAdapterMock;

    private string $sourceType;
    private string $fileName;
    private string $resourceDir;
    private XmlFileReaderService $xmlReaderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ftpAdapterMock = $this->createMock(IFTPAdapter::class);

        $this->resourceDir = __DIR__ . '/data/';
        $ftpHost = 'validServer';
        $ftpUser = 'user';
        $ftpPassword = 'pass';

        $this->sourceType = AppConstants::OPTION_SOURCE_LOCAL;
        $this->fileName = 'employee_test.xml';

        $this->xmlReaderService = new XmlFileReaderService(
            $this->ftpAdapterMock,
            $this->resourceDir,
            $ftpHost,
            $ftpUser,
            $ftpPassword
        );

    }

    /** @test */
    public function it_read_file_from_local_dir()
    {
        $arrayData = $this->xmlReaderService->read(
            $this->sourceType,
            $this->fileName
        );
        $this->assertSame(count($arrayData), count($this->getXmlData()));
    }

    /** @test */
    public function it_read_file_from_ftp(){

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(true);
        $this->ftpAdapterMock->method('getFile')->willReturn(true);

        $arrayData = $this->xmlReaderService->read(
            AppConstants::OPTION_SOURCE_FTP,
            $this->fileName
        );

        $this->assertSame(count($arrayData), count($this->getXmlData()));
    }

    /** @test */
    public function it_checks_non_existing_file_exception(){
        $this->expectException(FileNotExistException::class);

        $invalidFileName = 'employee.xml';
        $this->xmlReaderService->read(
            $this->sourceType,
            $invalidFileName
        );
    }

    /** @test */
    public function ftp_connection_refused_exception(){
        $this->expectException(FTPServerConnectionRefusedException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(false);
        $this->xmlReaderService->read(
            AppConstants::OPTION_SOURCE_FTP,
            $this->fileName
        );
    }

    /** @test */
    public function invalid_xml_content_parsing_error(){
        $this->expectError(ParseError::class);

        $errorFilename = 'employee_error.xml';
        $this->xmlReaderService->read(
            $this->sourceType,
            $errorFilename
        );

    }

    private function getXmlData(): SimpleXMLElement|bool {
        $fileContent = file_get_contents($this->resourceDir . $this->fileName);
        return simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
    }
}