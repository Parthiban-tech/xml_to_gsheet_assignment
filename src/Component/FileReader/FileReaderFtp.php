<?php

declare(strict_types=1);

namespace App\Component\FileReader;

use App\Interfaces\FileReaderInterface;
use Generator;


class FileReaderFtp implements FileReaderInterface
{
    private string $ftpConnectionUrl;
    private XmlFileReader $xmlFileReader;

    public function __construct(
        XmlFileReader $xmlFileReader,
        string $ftpConnectionUrl)
    {
        $this->ftpConnectionUrl = $ftpConnectionUrl;
        $this->xmlFileReader = $xmlFileReader;
    }

    public function read(string $xmlFileName): Generator {
        return $this->xmlFileReader->xmlReader($this->ftpConnectionUrl . $xmlFileName);
    }
}