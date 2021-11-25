<?php

declare(strict_types=1);

namespace App\Service;

class XmlDataTransformer
{

    public function transform($values): array
    {
        $header = array_keys($values[0]);
        $row_data = array_map(function($n) {
            return array_values($n);
        }, $values);
        array_unshift($row_data, $header);
        return $row_data;
    }
}