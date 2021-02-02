<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    private function dir($filename)
    {
        return "tests/fixtures/$filename";
    }

    public function testGenDiffJsonToJsonFormatStylish()
    {
        $source = __DIR__ . "/fixtures/compareStylishRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.json'), self::dir('file2rec.json'), 'stylish'));
    }
    public function testGenDiffJsonToJsonFormatPlain()
    {
        $source = __DIR__ . "/fixtures/comparePlainRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.json'), self::dir('file2rec.json'), 'plain'));
    }
    public function testGenDiffYamlToYamlFormatStylish()
    {
        $source = __DIR__ . "/fixtures/compareStylishRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.yml'), self::dir('file2rec.yml'), 'stylish'));
    }
    public function testGenDiffJsonToYamlFormatPlain()
    {
        $source = __DIR__ . "/fixtures/comparePlainRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.json'), self::dir('file2rec.yml'), 'plain'));
    }
    public function testGenDiffJsonToYamlFormatStylish()
    {
        $source = __DIR__ . "/fixtures/compareStylishRecursiveResult.txt";
        $expected5 = file_get_contents($source);
        $this->assertEquals($expected5, genDiff(self::dir('file1rec.json'), self::dir('file2rec.yml'), 'stylish'));
    }
    public function testGenDiffJsonToJsonFormatJson()
    {
        $source = __DIR__ . "/fixtures/compareJsonRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.json'), self::dir('file2rec.json'), 'json'));
    }
    public function testGenDiffJsonToYamlFormatJson()
    {
        $source = __DIR__ . "/fixtures/compareJsonRecursiveResult.txt";
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff(self::dir('file1rec.json'), self::dir('file2rec.yml'), 'json'));
    }
}
