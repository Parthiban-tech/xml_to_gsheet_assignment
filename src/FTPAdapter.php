<?php

namespace App;

use App\Interfaces\IFTPAdapter;
use Google\Service\Resource;

class FTPAdapter implements IFTPAdapter
{

    public function connect(string $ftpServer)
    {
        return ftp_connect($ftpServer);
    }

    public function login($connection, string $userName, string $password): bool
    {
        return ftp_login($connection, $userName, $password);
    }

    public function enablePassiveMode($connection): bool
    {
        return ftp_pasv($connection, TRUE);
    }

    public function getFile($connection, string $resourceDir, string $xmlFileName): bool
    {
        return ftp_get($connection, $resourceDir . $xmlFileName, $xmlFileName, FTP_BINARY);
    }

    public function closeConnection($connection): bool
    {
        return ftp_close($connection);
    }

}