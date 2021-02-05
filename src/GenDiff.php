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
    $sortedKeys = array_values(sortBy($uniqueKeys, function ($key) {
        return $key;
    }));

    $data = array_map(function ($key) use ($firstData, $secondData) {
        if (!property_exists($secondData, $key)) {
            print_r(['key' => $key, 'value' => $firstData->$key, 'type' => 'removed']);
            return ['key' => $key, 'value' => $firstData->$key, 'type' => 'removed'];
        }
        if (!property_exists($firstData, $key)) {
            print_r(['key' => $key, 'value' => $secondData->$key, 'type' => 'added']);
            return ['key' => $key, 'value' => $secondData->$key, 'type' => 'added'];
        }
        $nodeFirst = $firstData->$key;
        $nodeSecond = $secondData->$key;
        if (is_object($nodeFirst) && is_object($nodeSecond)) {

            //print_r(['key' => $key, 'type' => 'nested', 'children' => buildTree($nodeFirst, $nodeSecond)]);




            return ['key' => $key, 'type' => 'nested', 'children' => buildTree($nodeFirst, $nodeSecond)];
        }
        if ($nodeFirst === $nodeSecond) {
            return  ['key' => $key, 'value' => $nodeFirst, 'type' => 'unchanged'];
        }
        if ($nodeFirst !== $nodeSecond) {
            return ['key' => $key, 'oldValue' => $nodeFirst, 'newValue' => $nodeSecond, 'type' => 'changed'];
        }
    }, $sortedKeys);
    return $data;
}

//function sortKeys($data)
//{
//    $sortedKeys = array_values(sortBy($data, function ($key) {
//        return $key;
//    }));
//    return $sortedKeys;
//}
