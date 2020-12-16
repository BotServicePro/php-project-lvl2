<?php

namespace Differ\GenDiffTest;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use PHPUnit\Framework\TestCase;

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
$firstTest = new GenDiffTest;
$firstTest->diffTest();