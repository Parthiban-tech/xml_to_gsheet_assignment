<?php

declare(strict_types=1);

namespace App\Utility\FileDownloader;

use App\Utility\FileDownloader\Exception\FTPGetFileToLocalException;
use App\Utility\FileDownloader\Exception\FTPLoginFailedException;
use App\Utility\FileDownloader\Exception\FTPServerConnectionRefusedException;
use App\Interfaces\FileDownloader;
use App\Interfaces\FTPAdapterInterface;

class FTPFileDownloader implements FileDownloader
{

    private const FTP_SERVER = 'server';
    private const FTP_USER = 'user';
    private const FTP_PASSWORD = 'password';

    private FTPAdapterInterface $iFTPAdapter;
    private string $resourceDir;
    private string $ftpServer;
    private string $ftpUser;
    private string $ftpPassword;

    public function __construct(
        FTPAdapterInterface $iFTPAdapter,
        string              $resourceDir,
        array               $ftp
    )
    {
        $this->iFTPAdapter = $iFTPAdapter;
        $this->resourceDir = $resourceDir;
        $this->ftpServer = $ftp[self::FTP_SERVER];
        $this->ftpUser = $ftp[self::FTP_USER];
        $this->ftpPassword = $ftp[self::FTP_PASSWORD];
    }

    public function download(string $fileName)
    {
        $connection = $this->iFTPAdapter->connect($this->ftpServer);
        if(false === $connection){
            throw new FTPServerConnectionRefusedException("Unable to connect to FTP server: " . $connection);
        }

        if(false === $this->iFTPAdapter->login($connection, $this->ftpUser, $this->ftpPassword)){
            throw new FTPLoginFailedException("Unable to login server with user " . "'" . $this->ftpUser . "'");
        }

        $this->iFTPAdapter->enablePassiveMode($connection);
        if(false === $this->iFTPAdapter->getFile($connection, $this->resourceDir, $fileName)){
            throw new FTPGetFileToLocalException("Failed to get file or failed to move file to local. Local path: " . $this->resourceDir);
        }

        $this->iFTPAdapter->closeConnection($connection);
    }
}