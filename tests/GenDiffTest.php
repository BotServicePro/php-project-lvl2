<?php

namespace Differ\GenDiffTest;

use \PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function diffTest()
    {

        $expected1 = "{
                         - follow: false
                           host: hexlet.io
                         - proxy: 123.234.53.22
                         - timeout: 50
                         + timeout: 20
                         + verbose: true
                      }
        ";

        $this->assertEquals($expected1, genDiff('file1.json', 'file2.json'));


    }
}