<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        // Сравнение двух простых json
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff('file1.json', 'file2.json', $format = 'stylish'));

        // Сравнение двух простых yml
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.yml', 'file2.yml', $format = 'stylish'));

        // Сравнение простых yml и json
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.yml', 'file2.json', $format = 'stylish'));

        // Сравнение простых json и yml
        $source = __DIR__ . "/fixtures/compareResult.txt";
        $expected2 = file_get_contents($source);
        $this->assertEquals($expected2, genDiff('file1.json', 'file2.yml', $format = 'stylish'));

//        // Сравнение рекурсивных json и json
//        $source = __DIR__ . "/fixtures/compareRecursiveResult.txt";
//        $expected2 = file_get_contents($source);
//        $this->assertEquals($expected2, genDiff('file1rec.json', 'file2rec.json'));
//
//        // Сравнение рекурсивных yml и yml
//        $source = __DIR__ . "/fixtures/compareRecursiveResult.txt";
//        $expected2 = file_get_contents($source);
//        $this->assertEquals($expected2, genDiff('file1rec.yml', 'file2rec.yml'));
    }
}
