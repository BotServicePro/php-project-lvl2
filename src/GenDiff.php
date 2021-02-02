<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parser\extractData;
use function Differ\Formatters\astToStringConverter;

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
    $finalFormattedResult = astToStringConverter($differedData, $format);
    return $finalFormattedResult;
}

function diffData($firstFile, $secondFile)
{
    $firstFile = (array) $firstFile;
    $secondFile = (array) $secondFile;
    $uniqueKeys = array_keys(array_merge($firstFile, $secondFile));
    sort($uniqueKeys, SORT_NATURAL);
    $data = array_map(function ($key) use ($firstFile, $secondFile) {
        if (!array_key_exists($key, $firstFile)) {
            return ['key' => $key, 'value' => $secondFile[$key], 'type' => 'added'];
        }
        if (!array_key_exists($key, $secondFile)) {
            return ['key' => $key, 'value' => $firstFile[$key], 'type' => 'removed'];
        }
        $nodeFirst = $firstFile[$key];
        $nodeSecond = $secondFile[$key];
        if (is_object($nodeFirst) === true && is_object($nodeSecond) === true) {
            $children = diffData($nodeFirst, $nodeSecond);
            $arr = ['key' => $key, 'type' => 'nested', 'children' => $children];
            return $arr;
        }
        if ($nodeFirst === $nodeSecond) {
            return  ['key' => $key, 'value' => $nodeFirst, 'type' => 'unchanged'];
        }
        if ($nodeFirst !== $nodeSecond) {
            return ['key' => $key, 'oldValue' => $nodeFirst, 'newValue' => $nodeSecond, 'type' => 'changed'];
        }
    }, $uniqueKeys);
    return $data;
}
