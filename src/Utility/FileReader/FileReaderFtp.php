<?php

declare(strict_types=1);

namespace App\Utility\FileReader;

use App\Interfaces\FileDownloaderInterface;
use App\Interfaces\FileReaderInterface;
use Generator;


class FileReaderFtp implements FileReaderInterface
{

    private FileDownloaderInterface $fileDownloader;
    private FileReaderLocal $fileReaderLocal;

    public function __construct(
        FileReaderLocal         $fileReaderLocal,
        FileDownloaderInterface $fileDownloader)
    {
        $this->fileReaderLocal = $fileReaderLocal;
        $this->fileDownloader = $fileDownloader;
    }

    public function read(string $xmlFileName): Generator
    {
        $this->fileDownloader->download($xmlFileName);
        return $this->fileReaderLocal->read($xmlFileName);
    }
}