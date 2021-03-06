<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function render($tree): string
{
    $formattedTree = plain($tree);
    $finalResult = implode("\n", flattenAll($formattedTree));
    return $finalResult;
}

function plain($tree, $path = ''): array
{
    $result = array_map(function ($item) use ($path) {
        switch ($item['type']) {
            case 'added':
                $formattedValue = stringify($item['value']);
                return "Property '{$path}{$item['key']}' was added with value: $formattedValue";
            case 'removed':
                return "Property '{$path}{$item['key']}' was removed";
            case 'changed':
                $oldValue = stringify($item['oldValue']);
                $newValue = stringify($item['newValue']);
                return "Property '{$path}{$item['key']}' was updated. From $oldValue to $newValue";
            case 'unchanged':
                return [];
            case 'nested':
                $nestedPath = "{$path}{$item['key']}.";
                $children = $item['children'];
                return plain($children, $nestedPath);
            default:
                throw new \Exception("Error, could not identify 'type' in {$item}");
        }
    }, $tree);
    return $result;
}

function stringify($value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_int($value)) {
        return (string) $value;
    }
    if (is_array($value) || is_object($value)) {
        return "[complex value]";
    }
    return "'{$value}'";
}
