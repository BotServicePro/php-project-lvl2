<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parsers\extractData;
use function Differ\Formatters\astToStringConverter;
use function Funct\Collection\sortBy;
use function Funct\Collection\union;

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
    $firstData= extractData($path1);
    $secondData = extractData($path2);
    $differedData = diffData($firstData, $secondData);
    return astToStringConverter($differedData, $format);
}

function diffData($firstData, $secondData)
{
    $keysFromFirstData = array_keys(get_object_vars($firstData));
    $keysFromSecondData = array_keys(get_object_vars($secondData));
    $uniqueKeys = union($keysFromFirstData, $keysFromSecondData);
    $uniqueKeys = array_values(sortBy($uniqueKeys, function ($key) {
        return $key;
    }));
    $firstData = (array) $firstData;
    $secondData = (array) $secondData;
    $data = array_map(function ($key) use ($firstData, $secondData) {
        if (!array_key_exists($key, $firstData)) {
            if (is_object($secondData[$key])) {
                $result = keySorter($secondData[$key]);
                return ['key' => $key, 'value' => $result, 'type' => 'added'];
            } else {
                return ['key' => $key, 'value' => $secondData[$key], 'type' => 'added'];
            }
        }
        if (!array_key_exists($key, $secondData)) {
            return ['key' => $key, 'value' => $firstData[$key], 'type' => 'removed'];
        }
        $nodeFirst = $firstData[$key];
        $nodeSecond = $secondData[$key];
        if (is_object($nodeFirst) && is_object($nodeSecond)) {
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