<?php

declare(strict_types=1);

namespace App\Interfaces;

interface FTPAdapterInterface
{

    public function connect(string $ftpServer);

    public function login($connection, string $userName, string $password): bool;

    public function enablePassiveMode($connection): bool;

    public function getFile($connection, string $resourceDir, string $xmlFileName): bool;

    public function closeConnection($connection): bool;

}