<?php

declare(strict_types=1);

namespace App\Interfaces;

interface DataTransporterInterface
{
    public function transport(string $file, string $fromSource);
}
