<?php

declare(strict_types=1);

namespace App\Component\FileReader;

use App\Component\FileReader\Exception\FileNotExistException;
use App\Interfaces\FileReaderInterface;
use Generator;

class FileReaderLocal implements FileReaderInterface
{
    private string $resourceDir;
    private XmlFileReader $xmlFileReader;

    public function __construct( string $resourceDir, XmlFileReader $xmlFileReader)
    {
        $this->resourceDir = $resourceDir;
        $this->xmlFileReader = $xmlFileReader;
    }

    public function read(string $xmlFileName): Generator
    {
        $xmlAbsPath  = $this->resourceDir . $xmlFileName;

        if(false == file_exists($xmlAbsPath)) {
            throw new FileNotExistException("File does not exist in path: " . $xmlAbsPath);
        }

        return $this->xmlFileReader->xmlReader($xmlAbsPath);
    }
}