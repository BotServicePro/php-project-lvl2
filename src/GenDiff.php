<?php

namespace php\project\lvl2\GenDiff;

use Exception;
use function Funct\Collection\flattenAll;


$path1 = __DIR__ . '/file1.json';
$path2 = __DIR__ . '/file2.json';


function genDiff ($path1, $path2)
{
    if (is_file($path1) === true && is_file($path2) === true) {
        $readFirstFile = file_get_contents($path1);
        $readSecondFile = file_get_contents($path2);
        $decodedFirstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива а не объекта
        //ksort($decodedFirstFile, SORT_REGULAR);
        $decodedSecondFile = json_decode($readSecondFile, $associative = true);
        //ksort($decodedSecondFile, SORT_REGULAR);

        $wasDeletedTemp = array_diff_key($decodedFirstFile, $decodedSecondFile); // то что было удалено из первого массива
        //print_r($wasDeletedTemp);
        foreach ($wasDeletedTemp as $key => $value) {
            $wasDeleted[$key . ' -'] = var_export($value, true);
        }

        $wasNotChangedTemp = array_intersect_assoc($decodedFirstFile, $decodedSecondFile); // то что не изменилось в обоих массивах
        foreach ($wasNotChangedTemp as $key => $value) {
            $wasNotChanged[$key . '   '] = var_export($value, true);
        }

        $wasAddedTemp = array_diff_key($decodedSecondFile, $decodedFirstFile); // то что добавилось во второй массив
        foreach ($wasAddedTemp as $key => $value) {
            $wasAdded[$key . ' +'] = var_export($value, true);
        }

        $wasChanged = [];
        foreach ($decodedFirstFile as $key => $value) {
            if (array_key_exists($key, $decodedSecondFile) === true && $decodedFirstFile[$key] !== $decodedSecondFile[$key]) {
                $wasChanged[$key . ' -'] = $decodedFirstFile[$key];
                $wasChanged[$key . ' +'] = $decodedSecondFile[$key];
            }
        }

        $result = array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);
        ksort ($result);
        print_r($result);

//        $result = "{" . "\n" . implode("\n", $result) . "\n" . "}";
//        print_r($result);
        //$result = str_replace("'", '', $result);
        return $result;
    } elseif (!is_readable($path1) || !is_readable($path2)) {
        throw new Exception("'{$path1}' or '{$path2}' is not readable");
    }
    return null;
}

try {
    genDiff($path1, $path2);
} catch (Exception $e) {
}
