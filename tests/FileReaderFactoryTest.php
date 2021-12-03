<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class FileReaderFactoryTest extends TestCase
{

    /** @test */
    public function test_get_local_file_reader(){

        $this->assertTrue(true);

    }

    /** @test */
    public function test_get_ftp_file_reader(){

        $this->assertTrue(true);

    }

    /** @test */
    public function test_invalid_file_reader(){

        $this->assertTrue(true);

    }

}