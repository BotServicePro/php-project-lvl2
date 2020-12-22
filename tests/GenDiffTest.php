<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $source = __DIR__ . "/fixtures/difftest.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff('file1.json', 'file2.json'));
    }
}
