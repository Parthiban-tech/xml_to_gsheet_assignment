<?php

declare(strict_types=1);

namespace App\Interfaces;

use Generator;

interface SpreadSheetInterface{

    public function export(Generator $items);

}