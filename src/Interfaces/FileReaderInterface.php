<?php

declare(strict_types=1);

namespace App\Interfaces;

use Generator;

interface FileReaderInterface{
    public function read(string $xmlFileName): Generator;
}
