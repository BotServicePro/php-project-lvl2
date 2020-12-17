<?php

namespace Differ\GenDiffTest;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    include_once $autoloadPath1;
} else {
    include_once $autoloadPath2;
}

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function diffTest()
    {
        $path1 = "fixtures/difftest.txt";
        $expected1 = file_get_contents($path1);
        $this->assertEquals($expected1, genDiff('file1.json', 'file2.json'));
    }
}
$firstTest = new GenDiffTest;
$firstTest->diffTest();
