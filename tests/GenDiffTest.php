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
        $path = $this->makePath('compareStylish.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'stylish'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testPlain($firstFile, $secondFile)
    {
        $path = $this->makePath('comparePlain.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'plain'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testJson($firstFile, $secondFile)
    {
        $path = $this->makePath('compareJson.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'json'));
    }

    private function makePath($filename)
    {
        return "tests/fixtures/$filename";
    }

    public function additionProvider()
    {
        return [
            [$this->makePath('file1.json'), $this->makePath('file2.json')],
            [$this->makePath('file1.yml'), $this->makePath('file2.yml')],
            [$this->makePath('file1.json'), $this->makePath('file2.yml')],
        ];
    }
}
