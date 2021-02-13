<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Funct\Collection\sortBy;
use function Funct\Collection\union;

function readFile($path): array
{
    if (!file_exists($path)) {
        throw new Exception("File not found. Wrong filepath is: $path");
    }
    return ['fileData' => file_get_contents($path), 'extension' => pathinfo($path, PATHINFO_EXTENSION)];
}

function genDiff($path1, $path2, $format = 'stylish'): string
{
    $firstPath = readFile($path1);
    $secondPath = readFile($path2);
    $firstData = parse($firstPath['fileData'], $firstPath['extension']);
    $secondData = parse($secondPath['fileData'], $secondPath['extension']);
    $differedTree = buildTree($firstData, $secondData);
    return format($differedTree, $format);
}

function buildTree($firstData, $secondData): array
{
    $uniqueKeys = union(array_keys(get_object_vars($firstData)), array_keys(get_object_vars($secondData)));
    $sortedUniqueKeys = array_values(sortBy($uniqueKeys, fn ($key) => $key));
    $data = array_map(function ($key) use ($firstData, $secondData) {
        if (!property_exists($secondData, $key)) {
            return ['key' => $key, 'value' => $firstData->$key, 'type' => 'removed'];
        }
        if (!property_exists($firstData, $key)) {
            return ['key' => $key, 'value' => $secondData->$key, 'type' => 'added'];
        }
        if (is_object($firstData->$key) && is_object($secondData->$key)) {
            return ['key' => $key, 'type' => 'nested', 'children' => buildTree($firstData->$key, $secondData->$key)];
        }
        if ($firstData->$key !== $secondData->$key) {
            return [
                'key' => $key,
                'oldValue' => $firstData->$key,
                'newValue' => $secondData->$key,
                'type' => 'changed'
            ];
        }
        return  ['key' => $key, 'value' => $firstData->$key, 'type' => 'unchanged'];
    }, $sortedUniqueKeys);
    return $data;
}
