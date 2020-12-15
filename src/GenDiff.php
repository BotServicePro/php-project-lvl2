<?php

namespace Differ\GenDiff; // Differ мы сами указываем название! Любое!

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use function Funct\Collection\flattenAll;

function genDiff($path1, $path2)
{

    $path1 = '../' . $path1;
    $path2 = '../' . $path2;

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
