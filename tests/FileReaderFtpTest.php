<?php

declare(strict_types=1);

namespace App\Tests;

use App\Utility\FileDownloader\Exception\FTPGetFileToLocalException;
use App\Utility\FileDownloader\Exception\FTPLoginFailedException;
use App\Utility\FileDownloader\Exception\FTPServerConnectionRefusedException;
use App\Utility\FileReader\FileReaderFtp;
use App\Utility\FileReader\FileReaderLocal;
use App\Interfaces\FTPAdapterInterface;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class FileReaderFtpTest extends TestCase
{
    private FTPAdapterInterface $ftpAdapterMock;
    private FileReaderFtp $fileReaderFtp;
    private string $resourceDir;

    private string $fileName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ftpAdapterMock = $this->createMock(FTPAdapterInterface::class);

        $this->resourceDir = __DIR__ . '/data/';

        $this->fileName = 'employee_test.xml';

        $fileReaderLocal = new FileReaderLocal($this->resourceDir);
        $this->fileReaderFtp = new FileReaderFtp(
            $fileReaderLocal,
            $this->ftpAdapterMock,
            $this->resourceDir,
            $this->getFtpDetails()
        );

    }

    /** @test */
    public function it_read_file_from_ftp(){

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(true);
        $this->ftpAdapterMock->method('getFile')->willReturn(true);

        $arrayData = $this->fileReaderFtp->read($this->fileName);

        $this->assertSame(count($arrayData), count($this->getXmlData()));
    }

    /** @test */
    public function ftp_connection_refused_exception(){
        $this->expectException(FTPServerConnectionRefusedException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(false);
        $this->fileReaderFtp->read($this->fileName);
    }

    /** @test */
    public function ftp_login_failure_exception(){
        $this->expectException(FTPLoginFailedException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(false);
        $this->fileReaderFtp->read($this->fileName);
    }

    /** @test */
    public function ftp_failed_to_get_data_exception(){
        $this->expectException(FTPGetFileToLocalException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(true);
        $this->ftpAdapterMock->method('getFile')->willReturn(false);

        $this->fileReaderFtp->read($this->fileName);
    }

    private function getFtpDetails(): array{
        return ['server' => 'validServer', 'user' => 'user', 'password' => 'pass'];
    }

    private function getXmlData(): SimpleXMLElement|bool {
        $fileContent = file_get_contents($this->resourceDir . $this->fileName);
        return simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
    }
}