<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parsers\extractData;
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

function genDiff($path1, $path2, $format = 'stylish')
{
    $path1 = makeFilePath($path1);
    $path2 = makeFilePath($path2);
    $firstaData = extractData($path1);
    $secondData = extractData($path2);
    $differedData = diffData($firstaData, $secondData);
    return astToStringConverter($differedData, $format);
}

function diffData($firstaData, $secondData)
{
    $firstaData = (array) $firstaData;
    $secondData = (array) $secondData;
    $uniqueKeys = array_keys(array_merge($firstaData, $secondData));
    sort($uniqueKeys, SORT_NATURAL);
    $data = array_map(function ($key) use ($firstaData, $secondData) {
        if (!array_key_exists($key, $firstaData)) {
            if (is_object($secondData[$key])) {
                $result = keySorter($secondData[$key]);
                return ['key' => $key, 'value' => $result, 'type' => 'added'];
            } else {
                return ['key' => $key, 'value' => $secondData[$key], 'type' => 'added'];
            }
        }
        if (!array_key_exists($key, $secondData)) {
            return ['key' => $key, 'value' => $firstaData[$key], 'type' => 'removed'];
        }
        $nodeFirst = $firstaData[$key];
        $nodeSecond = $secondData[$key];
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

function keySorter($data)
{
    $value = (array) $data;
    $keys = array_keys($value);
    sort($keys, SORT_NATURAL);
    $result = array_reduce($keys, function ($acc, $key) use ($value) {
        $acc[$key] = $value[$key];
        return $acc;
    }, []);
    return $result;
}
