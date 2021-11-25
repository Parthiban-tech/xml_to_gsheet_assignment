<?php

declare(strict_types=1);

namespace App\Interfaces;

interface DataExporterInterface
{
    public function export(string $fileSourceType, string $fileName);
}
