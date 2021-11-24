<?php

namespace App\Interfaces;

interface FileReaderInterface{

    public function read(string $sourceType, string $xmlFileName): array;

}
