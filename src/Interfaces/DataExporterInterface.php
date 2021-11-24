<?php

namespace App\Interfaces;

interface DataExporterInterface
{
    public function export(string $fileSourceType, string $fileName);
}
