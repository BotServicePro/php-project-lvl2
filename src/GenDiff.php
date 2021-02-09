<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parsers\extractData;
use function Differ\Formatters\astToStringConverter;
use function Funct\Collection\sortBy;
use function Funct\Collection\union;

function makeFilePath($path)
{
    if (!file_exists($path)) {
        throw new Exception("File not found. Wrong filepath is: $path");
    }
    //$path = $_SERVER['DOCUMENT_ROOT'] . '../' . $path;
    return $path;
}

function genDiff($path1, $path2, $format = 'stylish')
{
    $path1 = makeFilePath($path1);
    $path2 = makeFilePath($path2);
    $firstData = extractData($path1);
    $secondData = extractData($path2);
    $differedData = buildTree($firstData, $secondData);
    return astToStringConverter($differedData, $format);
}

function buildTree($firstData, $secondData)
{
    $keysFromFirstData = array_keys(get_object_vars($firstData));
    $keysFromSecondData = array_keys(get_object_vars($secondData));
    $uniqueKeys = union($keysFromFirstData, $keysFromSecondData);
    $sortedUniqueKeys = array_values(sortBy($uniqueKeys, function ($key) {
        return $key;
    }));

    $data = array_map(function ($key) use ($firstData, $secondData) {
        if (!property_exists($secondData, $key)) {
            return ['key' => $key, 'value' => $firstData->$key, 'type' => 'removed'];
        }
        if (!property_exists($firstData, $key)) {
            $value = is_object($secondData->$key) ? sortBy(get_object_vars($secondData->$key), function ($item) {
                if (!is_object($item)) {
                    return $item;
                }
            }) : $secondData->$key;
            return ['key' => $key, 'value' => $value, 'type' => 'added'];
        }
        $nodeFirst = $firstData->$key;
        $nodeSecond = $secondData->$key;
        if (is_object($nodeFirst) && is_object($nodeSecond)) {
            return ['key' => $key, 'type' => 'nested', 'children' => buildTree($nodeFirst, $nodeSecond)];
        }
        if ($nodeFirst !== $nodeSecond) {
            return ['key' => $key, 'oldValue' => $nodeFirst, 'newValue' => $nodeSecond, 'type' => 'changed'];
        }
        return  ['key' => $key, 'value' => $nodeFirst, 'type' => 'unchanged'];
    }, $sortedUniqueKeys);
    return $data;
}
