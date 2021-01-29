<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parser\extractData;
use function Differ\Formatters\formateIt;

function makeFilePath($path)
{
    if (!is_readable($path)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '../' . $path;
    }
    if (!file_exists($path)) {
        throw new Exception("First or second file not found. Wrong filepath is: $path");
    }
    return $path;
}

function genDiff($path1, $path2, $format)
{
    $path1 = makeFilePath($path1);
    $path2 = makeFilePath($path2);
    $firstFile = extractData($path1);
    $secondFile = extractData($path2);
    $differedData = diffData($firstFile, $secondFile);
    $finalFormattedResult = formateIt($differedData, $format);
    return $finalFormattedResult;
}

function diffData($firstFile, $secondFile)
{
    $firstFile = json_decode(json_encode($firstFile), true);
    $secondFile = json_decode(json_encode($secondFile), true);
    $uniqueKeys = array_keys(array_merge($firstFile, $secondFile));
    $data = array_map(function ($key) use ($firstFile, $secondFile) {
        // если текущий ключ не существует в первом файле, но присутствует во втором
        if (!array_key_exists($key, $firstFile)) {
            return ['key' => $key, 'value' => $secondFile[$key], 'type' => 'added'];
        // если текущего ключа нет во втором файле, (логично что он есть в первом)
        } elseif (!array_key_exists($key, $secondFile)) {
            return ['key' => $key, 'value' => $firstFile[$key], 'type' => 'removed'];
        }
        $nodeFirst = $firstFile[$key];
        $nodeSecond = $secondFile[$key];
        if (is_array($nodeFirst) === true && is_array($nodeSecond) === true) {
            $children = diffData($nodeFirst, $nodeSecond);
            // возможно придеться поменять  c type на status, что бы было легче перебирать в цикле
            $arr = ['key' => $key, 'type' => 'nested', 'children' => $children];
            usort($arr['children'], function ($a, $b) {
                return substr($a['key'], 0, 1) <=> substr($b['key'], 0, 1);
            });
            return $arr;
        }
        if ($nodeFirst === $nodeSecond) {
            return  ['key' => $key, 'value' => $nodeFirst, 'type' => 'unchanged'];
        } else {
            return ['key' => $key, 'oldValue' => $nodeFirst, 'newValue' => $nodeSecond, 'type' => 'changed'];
        }
    }, $uniqueKeys);
    return $data;
}
