<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flattenAll;
use function Differ\Parsers\parser;
use function Differ\Parsers\recursiveParser;

const STARTTOSORTFROMSYMBOL = 4;
const JSONEXTENSION = -4;
const YMLEXTENSION = -3;

function isMultidimensional($data)
{
    // функция должна вернуть true или false если объект/массивы многомерные
    foreach ($data as $item) {
        if (is_object($item) === true || is_array($item) === true) {
            // если хотя бы один элемент в массиве будет объектом или массивом - это многомерные данные
            return true;
        }
    }
    return false;
}

function fileExtensionDataExtractor($fileName)
{
    // извлекаем данные в зависимости от расширения файла
    if (substr($fileName, YMLEXTENSION) === 'yml') { // если это yml файлы
        $file = file_get_contents($fileName);
        $data = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    if (substr($fileName, JSONEXTENSION) === 'json') { // если это json
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

    // если хотя бы один из массивов многомерный - вызываем другой парсер
    if (isMultidimensional($firstFile) === true || isMultidimensional($secondFile) === true) {
        $parsedData = recursiveParser($firstFile, $secondFile);
    } else {
        $parsedData = parser($firstFile, $secondFile);
    }





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
