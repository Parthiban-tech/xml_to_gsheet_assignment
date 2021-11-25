<?php

declare(strict_types=1);

namespace App\Interfaces;

interface SpreadSheetInterface{

    public function create(): string;

    public function write(string $sheetId, array $data, string $range);

    public function read(string $sheetId, string $range);

    public function exportToSpreadsheet(array $spreadSheetData);

}