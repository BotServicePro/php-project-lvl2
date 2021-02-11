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
                $stringedData = stringify($item['value']);
                return "Property '{$path}{$item['key']}' was added with value: $stringedData";
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

function stringify($data)
{
    if ($data === null) {
        return strtolower(var_export($data, true));
    }
    if (is_bool($data)) {
        return var_export($data, true);
    }
    if (is_array($data)) {
        return "[complex value]";
    }
    if (is_string($data)) {
        return "'{$data}'";
    }
    if (is_double($data) || is_int($data)) {
        return "{$data}";
    }
    if (is_object($data)) {
        return "[complex value]";
    }
    return $data;
}
