<?php

declare(strict_types=1);

namespace App\Tests\data;


class DataProvider
{
    public function inputDataToExport(): array
    {
        return array(
            0 => array(
                0 => 'id',
                1 => 'name',
                2 => 'email',
                3 => 'role',
                4 => 'team',
            ),
            1 => array(
                0 => '1',
                1 => 'Ayesha',
                2 => 'ayesha@productsup.com',
                3 => 'Developer',
                4 => 'Tech',
            ),
            2 => array(
                0 => '2',
                1 => 'Nayan',
                2 => 'nayan@productsup.com',
                3 => 'Developer',
                4 => 'Tech',
            ),
            3 => array(
                0 => '3',
                1 => 'Partha',
                2 => 'partha@productsup.com',
                3 => 'Developer',
                4 => 'Tech',
            ),
        );
    }

    public function inputDataToTransformer(): array
    {
        return array(
            0 => array(
                'id' => '1',
                'name' => 'Ayesha',
                'email' => 'ayesha@productsup.com',
                'role' => 'Developer',
                'team' => 'Tech',
            ),
            1 => array(
                'id' => '2',
                'name' => 'Nayan',
                'email' => 'nayan@productsup.com',
                'role' => 'Developer',
                'team' => 'Tech',
            ),
            2 => array(
                'id' => '3',
                'name' => 'Partha',
                'email' => 'partha@productsup.com',
                'role' => 'Developer',
                'team' => 'Tech',
            ),
        );
    }
}