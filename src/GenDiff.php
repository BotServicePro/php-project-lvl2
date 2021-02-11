<?php

namespace Differ\GenDiff;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Funct\Collection\sortBy;
use function Funct\Collection\union;

function readFile($path)
{
    if (!file_exists($path)) {
        throw new Exception("File not found. Wrong filepath is: $path");
    }
    return ['fileData' => file_get_contents($path), 'extension' => pathinfo($path, PATHINFO_EXTENSION)];
}

function genDiff($path1, $path2, $format = 'stylish')
{
    $path1 = readFile($path1);
    $path2 = readFile($path2);
    $firstData = parse($path1['fileData'], $path1['extension']);
    $secondData = parse($path2['fileData'], $path2['extension']);
    $differedData = buildTree($firstData, $secondData);
    return format($differedData, $format);
}

function buildTree($firstData, $secondData)
{
    $keysFromFirstData = array_keys(get_object_vars($firstData));
    $keysFromSecondData = array_keys(get_object_vars($secondData));
    $uniqueKeys = union($keysFromFirstData, $keysFromSecondData);
    $sortedUniqueKeys = array_values(sortBy($uniqueKeys, fn ($key) => $key));
    $data = array_map(function ($key) use ($firstData, $secondData) {
        if (!property_exists($secondData, $key)) {
            return ['key' => $key, 'value' => $firstData->$key, 'type' => 'removed'];
        }
        if (!property_exists($firstData, $key)) {
            return ['key' => $key, 'value' => $secondData->$key, 'type' => 'added'];
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
