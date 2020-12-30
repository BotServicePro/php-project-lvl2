<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flattenAll;
use function Differ\Parsers\parser;

const STARTTOSORTFROMSYMBOL = 4;

function fileExtensionDataExtractor($fileName)
{
    $jsonExtension = (int) -4;
    $ymlExtension = (int) -3;
    if (substr($fileName, $ymlExtension) === 'yml') {
        $file = file_get_contents($fileName);
        $data = (array) Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP); // результат как объект
    }
    if (substr($fileName, $jsonExtension) === 'json') {
        $file = file_get_contents($fileName);
        $data = json_decode($file, $associative = true);
    }
    return $data;
}

function genDiff($path1, $path2)
{
    // делаем пути
    $path1 = $_SERVER['DOCUMENT_ROOT'] . $path1;
    $path2 = $_SERVER['DOCUMENT_ROOT'] . $path2;

    if (!is_readable($path1) || !is_readable($path2)) {
        $path1 = '../' . $path1;
        $path2 = '../' . $path2;
    }

    $firstFile = fileExtensionDataExtractor($path1);
    $secondFile = fileExtensionDataExtractor($path2);
    $parsedData = parser($firstFile, $secondFile);
    $sortedArr = flattenAll($parsedData);
    usort($sortedArr, function ($a, $b) {
        return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
    });

    // сборка в финальный вид
    $stringWithSemicolons = "{" . "\n" . implode("\n", $sortedArr) . "\n" . "}";
    $finalResult = str_replace("'", '', $stringWithSemicolons);
    print_r($finalResult);
    return $finalResult;
}
