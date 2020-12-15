<?php

namespace Differ\GenDiff;

use Exception;

use function Funct\Collection\flattenAll;

function genDiff($path1, $path2)
{

    $path1 = '../' . $path1;
    $path2 = '../' . $path2;

    if (is_file($path1) === true && is_file($path2) === true) {
        $readFirstFile = file_get_contents($path1);
        $readSecondFile = file_get_contents($path2);
        $firstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива
        $secondFile = json_decode($readSecondFile, $associative = true);
        $wasDeletedTemp = array_diff_key($firstFile, $secondFile); // то что было удалено из первого
        foreach ($wasDeletedTemp as $key => $value) {
            $wasDeleted[] = ' - ' . $key . ': ' . var_export($value, true); // var_export фактический результат
        }

        $wasNotChangedTemp = array_intersect_assoc($firstFile, $secondFile);
        foreach ($wasNotChangedTemp as $key => $value) {
            $wasNotChanged[] = '   ' . $key . ': ' . var_export($value, true);
        }

        $wasAddedTemp = array_diff_key($secondFile, $firstFile); // то что добавилось во второй массив
        foreach ($wasAddedTemp as $key => $value) {
            $wasAdded[] = ' + ' . $key . ': ' . var_export($value, true);
        }

        foreach ($firstFile as $key => $value) {
            if (array_key_exists($key, $secondFile) === true && $firstFile[$key] !== $secondFile[$key]) {
                $wasChanged[] = [' - ' . $key .  ': ' . $firstFile[$key],
                    ' + ' . $key . ': ' . $secondFile[$key]];
            }
        }

        $mergedResults = array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);
        $sortedArr = flattenAll($mergedResults);
        usort($sortedArr, function ($a, $b) {
            return substr($a, 3, 1) <=> substr($b, 3, 1);
        });

        $stringWithSemicolons = "{" . "\n" . implode("\n", $sortedArr) . "\n" . "}" . "\n";
        $finalResult = str_replace("'", '', $stringWithSemicolons);
        print_r($finalResult);
        return $finalResult;
    } elseif (!is_readable($path1) || !is_readable($path2)) {
        throw new Exception("'{$path1}' or '{$path2}' is not readable");
    }
    return null;
}

//try {
//    genDiff($path1, $path2);
//} catch (Exception $e) {
//}
