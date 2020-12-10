<?php

namespace php\project\lvl2\GenDiff;

use Exception;
use function Funct\Collection\flatten;


$path1 = __DIR__ . '/file1.json';
$path2 = __DIR__ . '/file2.json';

function  differenceFinder($arr1, $arr2) { // функция поиска измененных значений в обоих массивах
    $result = [];
    foreach ($arr1 as $keyFromFirstFile => $valueFromFirstValue ) { // берем значения из первого файла
        foreach ($arr2 as $keyFromSecondFile => $valueFromSecondFile) { // берем значения из второго файла
            if ($valueFromFirstValue !== $valueFromSecondFile && $keyFromFirstFile === $keyFromSecondFile) { // если ключ остался но изменилось значение - записываем как измененное значене
                $var = var_export($valueFromFirstValue, true);
                $result[] = ['- ' . $keyFromFirstFile . ': ' . $var];
                $var = var_export($valueFromSecondFile, true);
                $result[] = ['+ ' . $keyFromSecondFile . ': ' . $var];
            }
        }
    }
    return  $result;
}

function genDiff ($path1, $path2)
{
    if (is_file($path1) === true && is_file($path2) === true) {
        $readFirstFile = file_get_contents($path1);
        $readSecondFile = file_get_contents($path2);
        $decodedFirstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива а не объекта
        ksort($decodedFirstFile, SORT_REGULAR);
        $decodedSecondFile = json_decode($readSecondFile, $associative = true);
        ksort($decodedSecondFile, SORT_REGULAR);
        //$result = [];

        $wasDeleted = array_diff_key($decodedFirstFile, $decodedSecondFile); // то что было удалено из первого массива
        foreach ($wasDeleted as $key => $value) {
            $wasDeleted[] = '- ' . $key . ': ' . var_export($value, true);
        }

        $wasDeleted = array_intersect_key($wasDeleted, array_flip(array_filter(array_keys($wasDeleted), 'is_numeric')));
        $wasNotChanged = array_intersect_assoc($decodedFirstFile, $decodedSecondFile); // то что не изменилось в обоих массивах
        foreach ($wasNotChanged as $key => $value) {
            $wasNotChanged[] = '  ' . $key . ': ' . var_export($value, true);
        }
        $wasNotChanged = array_intersect_key($wasNotChanged, array_flip(array_filter(array_keys($wasNotChanged), 'is_numeric')));

        $wasAdded = array_diff_key($decodedSecondFile, $decodedFirstFile); // то что добавилось во второй массив
        foreach ($wasAdded as $key => $value) {
            $wasAdded[] = '+ ' . $key . ': ' . var_export($value, true);
        }

        $wasAdded = array_intersect_key($wasAdded, array_flip(array_filter(array_keys($wasAdded), 'is_numeric')));
        $wasChanged = differenceFinder($decodedFirstFile, $decodedSecondFile); // то что осталось но изменилось
        $wasChanged = flatten($wasChanged);
        $result = array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);

        print_r($result);
        //$result = "{" . "\n" . $result  . "}";
        //$result = str_replace("'", '', $result);
        return $result;
    } elseif (!is_readable($path1) || !is_readable($path2)) {
        throw new Exception("'{$path1}' or '{$path2}' is not readable");
    }
    return null;
}
genDiff($path1, $path2);
