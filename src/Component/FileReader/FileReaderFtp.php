<?php

declare(strict_types=1);

namespace App\Component\FileReader;

use App\Component\FileReader\Exception\FTPGetFileToLocalException;
use App\Component\FileReader\Exception\FTPLoginFailedException;
use App\Component\FileReader\Exception\FTPServerConnectionRefusedException;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\FTPAdapterInterface;


class FileReaderFtp implements FileReaderInterface
{
    private const FTP_SERVER = 'server';
    private const FTP_USER = 'user';
    private const FTP_PASSWORD = 'password';

    private FTPAdapterInterface $iFTPAdapter;
    private FileReaderLocal $fileReaderLocal;
    private string $resourceDir;
    private string $ftpServer;
    private string $ftpUser;
    private string $ftpPassword;


    public function __construct(
        FileReaderLocal     $fileReaderLocal,
        FTPAdapterInterface $iFTPAdapter,
        string              $resourceDir,
        array               $ftp
    )
    {
        $this->fileReaderLocal = $fileReaderLocal;
        $this->iFTPAdapter = $iFTPAdapter;
        $this->resourceDir = $resourceDir;
        $this->ftpServer = $ftp[self::FTP_SERVER];
        $this->ftpUser = $ftp[self::FTP_USER];
        $this->ftpPassword = $ftp[self::FTP_PASSWORD];
    }

    public function read(string $xmlFileName): array
    {
        $connection = $this->iFTPAdapter->connect($this->ftpServer);
        if(false === $connection){
            throw new FTPServerConnectionRefusedException("Unable to connect to FTP server: " . $connection);
        }

        if(false === $this->iFTPAdapter->login($connection, $this->ftpUser, $this->ftpPassword)){
            throw new FTPLoginFailedException("Unable to login server with user " . "'" . $this->ftpUser . "'");
        }

        $this->iFTPAdapter->enablePassiveMode($connection);
        if(false === $this->iFTPAdapter->getFile($connection, $this->resourceDir, $xmlFileName)){
            throw new FTPGetFileToLocalException("Failed to get file or failed to move file to local. Local path: " . $this->resourceDir);
        }

        $this->iFTPAdapter->closeConnection($connection);

        return $this->fileReaderLocal->read($xmlFileName);
    }
}