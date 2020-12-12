<?php

namespace php\project\lvl2\GenDiff;

use Exception;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

$path1 = __DIR__ . '/file1.json';
$path2 = __DIR__ . '/file2.json';

function genDiff ($path1, $path2)
{
    if (is_file($path1) === true && is_file($path2) === true) {
        $readFirstFile = file_get_contents($path1);
        $readSecondFile = file_get_contents($path2);
        $decodedFirstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива а не объекта
        $decodedSecondFile = json_decode($readSecondFile, $associative = true);
        $wasDeletedTemp = array_diff_key($decodedFirstFile, $decodedSecondFile); // то что было удалено из первого массива
        foreach ($wasDeletedTemp as $key => $value) {
            $wasDeleted[] = ' - ' . $key . ': ' . var_export($value, true); // var_export отвечает за фактическое отображение результата
        }

        $wasNotChangedTemp = array_intersect_assoc($decodedFirstFile, $decodedSecondFile); // то что не изменилось в обоих массивах
        foreach ($wasNotChangedTemp as $key => $value) {
            $wasNotChanged[] = '   ' . $key . ': ' . var_export($value, true);
        }

        $wasAddedTemp = array_diff_key($decodedSecondFile, $decodedFirstFile); // то что добавилось во второй массив
        foreach ($wasAddedTemp as $key => $value) {
            $wasAdded[] = ' + ' . $key . ': ' . var_export($value, true);
        }

        foreach ($decodedFirstFile as $key => $value) {
            if (array_key_exists($key, $decodedSecondFile) === true && $decodedFirstFile[$key] !== $decodedSecondFile[$key]) {
                $wasChanged[] = [' - ' . $key .  ': ' . $decodedFirstFile[$key], ' + ' . $key . ': ' . $decodedSecondFile[$key]];
            }
        }

        $mergedResults = array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($mergedResults)); // вместо flattenAll
        $sortedArr = iterator_to_array($iterator, false);
        usort($sortedArr, function($a, $b) { // сортируем по пользовательской функции начиная с 4ого символа
            return substr($a, 3, 1) <=> substr($b, 3, 1);
        });

        $stringWithSemicolons = "{" . "\n" . implode("\n", $sortedArr) . "\n" . "}";
        $finalResult = str_replace("'", '', $stringWithSemicolons);
        print_r($finalResult);
        return $finalResult;

    } elseif (!is_readable($path1) || !is_readable($path2)) {
        throw new Exception("'{$path1}' or '{$path2}' is not readable");
    }
    return null;
}

try {
    genDiff($path1, $path2);
} catch (Exception $e) {
}
