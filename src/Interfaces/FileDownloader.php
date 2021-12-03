<?php

declare(strict_types=1);

namespace App\Interfaces;

interface FileDownloader
{
    public function download(string $fileName);
}