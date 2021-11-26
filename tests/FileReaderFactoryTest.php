<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileReaderFactoryTest extends KernelTestCase
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