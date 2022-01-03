<?php

namespace App\Interfaces;

use Generator;

interface DataTransformer
{
    public function transform(Generator $records): Generator;
}