<?php

namespace php\project\lvl2\GenDiff;

$path1 = __DIR__ . '/file1.json';
$path2 = __DIR__ . '/file2.json';

function genDiff ($path1, $path2)
{
    if (is_file($path1) === true && is_file($path2) === true) {
        $readFirstFile = file_get_contents($path1);
        $readSecondFile = file_get_contents($path2);
        $decodedFirstFile = json_decode($readFirstFile, $associative = true); // тру означает возврат в виде массива а не объекта
        ksort($decodedFirstFile, SORT_REGULAR);
        $decodedSecondFile = json_decode($readSecondFile, $associative = true);
        ksort($decodedSecondFile, SORT_REGULAR);
        print_r($decodedSecondFile);
        $result = '';

        foreach ($decodedFirstFile as $keyFromFirstFile => $valueFromFirstValue ) { // берем значения из первого файла
            foreach ($decodedSecondFile as $keyFromSecondFile => $valueFromSecondFile) { // берем значения из второго файла
                if (array_key_exists($keyFromFirstFile, $decodedFirstFile) === true && array_key_exists($keyFromFirstFile, $decodedSecondFile) === false) { // если значение было но исчезло (по ключу)  - записываем как минус
                    $var = var_export($valueFromFirstValue, true);
                    $result .= "\t- $keyFromFirstFile: $var \n"; // var_export возвращает стринговое значение
                    break;
                } elseif ($valueFromFirstValue === $valueFromSecondFile) { // если значение осталось неизменным - записываем как НЕизменное
                    $var = var_export($valueFromFirstValue, true);
                    $result .= "\t  $keyFromFirstFile: $var \n"; // var_export возвращает стринговое значение
                    break;
                } elseif ($valueFromFirstValue !== $valueFromSecondFile && $keyFromFirstFile === $keyFromSecondFile) { // если ключ остался но изменилось значение - записываем как измененное значене
                    $var = var_export($valueFromFirstValue, true);
                    $result .= "\t- $keyFromFirstFile: $var \n";
                    $var = var_export($valueFromSecondFile, true);
                    $result .= "\t+ $keyFromFirstFile: $var \n";
                } elseif (array_key_exists($keyFromSecondFile, $decodedFirstFile) === false) { // если ключа небыло но появился - записываем как плюс
                    $var = var_export($valueFromSecondFile, true);
                    $result .= "\t+ $keyFromSecondFile: $var \n";
                }
            }
        }

        $result = "{" . "\n" . $result  . "}";
        print_r($result);
    }
    return $result;
}

genDiff($path1, $path2);
