<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testStylish($firstFile, $secondFile)
    {
        $source = self::makePath('compareStylish.txt');
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'stylish'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testPlain($firstFile, $secondFile)
    {
        $source = self::makePath('comparePlain.txt');
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'plain'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testJson($firstFile, $secondFile)
    {
        $source = self::makePath('compareJson.txt');
        $expected = file_get_contents($source);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'json'));
    }

    private function makePath($filename)
    {
        return "tests/fixtures/$filename";
    }

    public function additionProvider()
    {
        return [
            [self::makePath('file1rec.json'), self::makePath('file2rec.json')],
            [self::makePath('file1rec.yml'), self::makePath('file2rec.yml')],
            [self::makePath('file1rec.json'), self::makePath('file2rec.yml')],
        ];
    }
}
