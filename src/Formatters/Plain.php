<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function render($tree)
{
    $path = '';
    $stringedTree = plain($tree, $path);
    $formatedData = implode("\n", flattenAll($stringedTree));
    return $formatedData;
}

function plain($data, $path)
{
    $result = array_map(function ($item) use ($path) {
        switch ($item['type']) {
            case 'added':
                $formattedData = stringify($item['value']);
                return "Property '{$path}{$item['key']}' was added with value: $formattedData";
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
        }
    }, $data);
    return $result;
}

function stringify($value)
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        return "[complex value]";
    }
    if (is_object($value)) {
        return "[complex value]";
    }
    return (string) "'{$value}'";
}
