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
        $path = $this->getPath('compareStylish.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'stylish'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testPlain($firstFile, $secondFile)
    {
        $path = $this->getPath('comparePlain.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'plain'));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testJson($firstFile, $secondFile)
    {
        $path = $this->getPath('compareJson.txt');
        $expected = file_get_contents($path);
        $this->assertEquals($expected, genDiff($firstFile, $secondFile, 'json'));
    }

    private function getPath($filename)
    {
        return "tests/fixtures/$filename";
    }

    public function additionProvider()
    {
        return [
            [$this->getPath('file1.json'), $this->getPath('file2.json')],
            [$this->getPath('file1.yml'), $this->getPath('file2.yml')],
            [$this->getPath('file1.json'), $this->getPath('file2.yml')],
        ];
    }
}
