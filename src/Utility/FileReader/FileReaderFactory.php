<?php

namespace App\Utility\FileReader;

use App\Utility\FileReader\Exception\InvalidParameterException;
use App\Interfaces\FileReaderInterface;

class FileReaderFactory
{
    private array $fileReaderConfig;

    public function __construct(array $fileReaderConfig){
        $this->fileReaderConfig = $fileReaderConfig;
    }

    public function getReader(string $sourceType): FileReaderInterface{
        $readerService = $this->fileReaderConfig[$sourceType];
        if(empty($readerService)){
            throw new InvalidParameterException("Invalid source type : " . $sourceType);
        }
        return $readerService;
    }

}