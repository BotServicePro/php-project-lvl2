<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        // Сравнение двух json
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff('file1.json', 'file2.json'));

        // Сравнение двух yml
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.yml', 'file2.yml'));

        // Сравнение yml и json
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.yml', 'file2.json'));

        // Сравнение json и yml
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.json', 'file2.yml'));
    }
}
