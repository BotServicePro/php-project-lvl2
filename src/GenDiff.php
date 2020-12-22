<?php

namespace Differ\GenDiff;

use function Funct\Collection\flattenAll;

const STARTTOSORTFROMSYMBOL = 4;

function genDiff($path1, $path2)
{
    $path1 = $_SERVER['DOCUMENT_ROOT'] . $path1;
    $path2 = $_SERVER['DOCUMENT_ROOT'] . $path2;

    if (!is_readable($path1) || !is_readable($path2)) {
        $path1 = '../' . $path1;
        $path2 = '../' . $path2;
    }
    $readFirstFile = file_get_contents($path1);
    $readSecondFile = file_get_contents($path2);
    $firstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива
    $secondFile = json_decode($readSecondFile, $associative = true);
    $wasDeletedTemp = array_diff_key($firstFile, $secondFile); // то что было удалено из первого
    foreach ($wasDeletedTemp as $key => $value) {
        $wasDeleted[] = '  - ' . $key . ': ' . var_export($value, true); // var_export фактический результат
    }

    $wasNotChangedTemp = array_intersect_assoc($firstFile, $secondFile);
    foreach ($wasNotChangedTemp as $key => $value) {
        $wasNotChanged[] = '    ' . $key . ': ' . var_export($value, true);
    }

    $wasAddedTemp = array_diff_key($secondFile, $firstFile); // то что добавилось во второй массив
    foreach ($wasAddedTemp as $key => $value) {
        $wasAdded[] = '  + ' . $key . ': ' . var_export($value, true);
    }

    foreach ($firstFile as $key => $value) {
        if (array_key_exists($key, $secondFile) === true && $firstFile[$key] !== $secondFile[$key]) {
            $wasChanged[] = ['  - ' . $key .  ': ' . $firstFile[$key],
                '  + ' . $key . ': ' . $secondFile[$key]];
        }
    }

    $mergedResults = array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);
    $sortedArr = flattenAll($mergedResults);
    usort($sortedArr, function ($a, $b) {
        return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
    });

    $stringWithSemicolons = "{" . "\n" . implode("\n", $sortedArr) . "\n" . "}";
    $finalResult = str_replace("'", '', $stringWithSemicolons);
    print_r($finalResult);
    return $finalResult;
}
