<?php

declare(strict_types=1);

namespace App\Service;

use JetBrains\PhpStorm\Pure;

class XmlDataTransformer
{
    #[Pure] public function transform($values): array
    {
        $exportData = [];
        foreach ($values as $key => $value){
            // Column header
            if(0 === $key)
                $exportData[] = $this->getKeysAsColHeader($value);
            // Row Data
            $exportData[] = array_values($value);
        }
        return $exportData;
    }

    private function getKeysAsColHeader($colData): array
    {
        return array_keys($colData);
    }
}