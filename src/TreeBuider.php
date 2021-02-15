<?php

namespace Differ\TreeBuilder;

use function Funct\Collection\sortBy;
use function Funct\Collection\union;

function buildTree(object $firstData, object $secondData): array
{
    $uniqueKeys = union(array_keys(get_object_vars($firstData)), array_keys(get_object_vars($secondData)));
    $sortedUniqueKeys = array_values(sortBy($uniqueKeys, fn ($key) => $key));
    $data = array_map(function (string $key) use ($firstData, $secondData): array {
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
