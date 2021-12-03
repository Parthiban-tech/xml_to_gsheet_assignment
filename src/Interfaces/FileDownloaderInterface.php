<?php

declare(strict_types=1);

namespace App\Interfaces;

interface FileDownloaderInterface
{
    public function download(string $fileName);
}