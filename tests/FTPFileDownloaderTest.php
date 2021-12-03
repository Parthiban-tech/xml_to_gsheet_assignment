<?php

declare(strict_types=1);

namespace App\Tests;

use App\Interfaces\FTPAdapterInterface;
use App\Utility\FileDownloader\Exception\FTPGetFileToLocalException;
use App\Utility\FileDownloader\Exception\FTPLoginFailedException;
use App\Utility\FileDownloader\Exception\FTPServerConnectionRefusedException;
use App\Utility\FileDownloader\FTPFileDownloader;
use PHPUnit\Framework\TestCase;

class FTPFileDownloaderTest extends TestCase
{

    private FTPAdapterInterface $ftpAdapterMock;
    private FTPFileDownloader $ftpFileDownloader;

    private string $fileName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ftpAdapterMock = $this->createMock(FTPAdapterInterface::class);

        $resourceDir = __DIR__ . '/data/';

        $this->fileName = 'employee_test.xml';

        $this->ftpFileDownloader = new FTPFileDownloader(
            $this->ftpAdapterMock,
            $resourceDir,
            $this->getFtpDetails()
        );
    }

    /** @test */
    public function it_read_file_from_ftp(){

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(true);
        $this->ftpAdapterMock->method('getFile')->willReturn(true);

        $downloadStatus = $this->ftpFileDownloader->download($this->fileName);

        $this->assertTrue($downloadStatus);
    }

    /** @test */
    public function ftp_connection_refused_exception(){
        $this->expectException(FTPServerConnectionRefusedException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(false);
        $this->ftpFileDownloader->download($this->fileName);
    }

    /** @test */
    public function ftp_login_failure_exception(){
        $this->expectException(FTPLoginFailedException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(false);
        $this->ftpFileDownloader->download($this->fileName);
    }

    /** @test */
    public function ftp_failed_to_get_data_exception(){
        $this->expectException(FTPGetFileToLocalException::class);

        $this->ftpAdapterMock->method('connect')->willReturn(true);
        $this->ftpAdapterMock->method('login')->willReturn(true);
        $this->ftpAdapterMock->method('getFile')->willReturn(false);

        $this->ftpFileDownloader->download($this->fileName);
    }

    private function getFtpDetails(): array{
        return ['server' => 'validServer', 'user' => 'user', 'password' => 'pass'];
    }
}