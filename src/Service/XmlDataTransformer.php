<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\DataTransformer;
use Generator;

class XmlDataTransformer implements DataTransformer
{

    public function transform(Generator $records): Generator
    {
        yield $this->getKeysAsColHeader($records->current());

        foreach ($records as $key => $record) {
            yield array_values($record);
        }
    }

    private function getKeysAsColHeader($colData): array
    {
        return array_keys($colData);
    }
}