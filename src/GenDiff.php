<?php

namespace Differ\GenDiff;

use function Differ\Parser\parser;

const STARTTOSORTFROMSYMBOL = 4;

function genDiff($path1, $path2) // главная функция
{
    if (!is_readable($path1) || !is_readable($path2)) {
        $path1 = $_SERVER['DOCUMENT_ROOT'] . '../' . $path1;
        $path2 = $_SERVER['DOCUMENT_ROOT'] . '../' . $path2;
    } elseif (!file_exists($path1) || !file_exists($path2)) {
        throw new \Exception("First or second file not found.");
    }

    $firstFile = parser($path1); // получаем данные из файла в том виде в каком они есть
    $secondFile = parser($path2); // получаем данные из файла в том виде в каком они есть
    $differedData = diffData($firstFile, $secondFile); // получаем различия файлов и плоских и жирных
    $finalResult = formatter($differedData);

    return $finalResult;
}

function diffData($firstFile, $secondFile)
{
    $firstFile = (array) $firstFile;
    $secondFile = (array) $secondFile;
    $uniqueKeys = array_keys(array_merge($firstFile, $secondFile));
    // через замыкание use
    $data = array_map(function ($key) use ($firstFile, $secondFile) {
        // если текущий ключ не существует в первом файле, но присутствует во втором
        if (!array_key_exists($key, $firstFile)) {
            return ['key' => $key, 'value' => $secondFile[$key], 'status' => 'added'];
        // если текущего ключа нет во втором файле, (логично что он есть в первом)
        } elseif (!array_key_exists($key, $secondFile)) {
            return ['key' => $key, 'value' => $firstFile[$key], 'status' => 'removed'];
        }

        $nodeFirst = $firstFile[$key];
        $nodeSecond = $secondFile[$key];

        print_r($nodeFirst);
        print_r($nodeSecond);

        if (is_object($nodeFirst) && is_object($nodeSecond)) {
            $children = diffData($nodeFirst, $nodeSecond);
            return ['key' => $key, 'type' => 'nested', 'children' => $children];
        }
        if ($nodeFirst === $nodeSecond) {
            return  ['key' => $key, 'value' => $nodeFirst, 'status' => 'not changed'];
        } else {
            return ['key' => $key, 'newValue' => $nodeSecond, 'oldValue' => $nodeFirst, 'status' => 'changed'];
        }
    }, $uniqueKeys);
    print_r($data);
    return $data;
}

function isMultidimensional($data)
{
    // функция должна вернуть true или false если объект/массивы многомерные
    foreach ($data as $item) {
        foreach ($item as $subItem) {
            if (is_array($subItem) === true || is_object($subItem) === true) {
                // если хотя бы один элемент в массиве будет объектом или массивом - это многомерные данные
                return true;
            }
        }
    }
    return false;
}

function formatter($data)
{
    //$data = json_decode(json_encode($data), true);
    //print_r($data);
    if (isMultidimensional($data) === false) { // если одномерные данные
        foreach ($data as $item) {
            if ($item['status'] === 'changed') {
                $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['oldValue'], true);
                $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['newValue'], true);
            }
            if ($item['status'] === 'not changed') {
                $tempResult[] = "    " . $item['key'] . ': ' . var_export($item['value'], true);
            }
            if ($item['status'] === 'added') {
                $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['value'], true);
            }
            if ($item['status'] === 'removed') {
                $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['value'], true);
            }
        }

        //print_r($tempResult);
        usort($tempResult, function ($a, $b) {
            return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
        });
        $tempResult = str_replace("'", '', $tempResult);
        //print_r($tempResult);
        $finalResult = "{" . "\n" . implode("\n", $tempResult) . "\n" . "}";
        //print_r($finalResult);
    } elseif (isMultidimensional($data) === true) { // если данные многомерные
        foreach ($data as $item) {
            //print_r($item);
            if ($item['status'] === 'changed') {
            }
            if ($item['status'] === 'not changed') {
                $tempResult[] = "    " . $item['key'] . ': ' . var_export($item['value'], true);
            }
            if ($item['status'] === 'added') {
                $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['value'], true);
            }
            if ($item['status'] === 'removed') {
                $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['value'], true);
            }
        }
    }
    //print_r($tempResult);
    return $finalResult;
}
