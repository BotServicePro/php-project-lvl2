<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flatten;
use function Funct\Collection\sortBy;

function render($tree)
{
    $formattedTree = stylish($tree);
    $finalResult = "{\n" . implode("\n", flatten($formattedTree)) . "\n}";
    $finalResult = str_replace("'", '', $finalResult);
    return $finalResult;
}

function stylish($tree, $depth = 1)
{
    $result = array_map(function ($item) use ($depth) {
        $indent = makeIndent($depth - 1);
        switch ($item['type']) {
            case 'added':
                $formattedValue = strigify($item['value'], $depth);
                return "$indent  + {$item['key']}: $formattedValue";
            case 'removed':
                $formattedValue = strigify($item['value'], $depth);
                return "$indent  - {$item['key']}: $formattedValue";
            case 'changed':
                $oldValue = strigify($item['oldValue'], $depth);
                $newValue = strigify($item['newValue'], $depth);
                $formattedNewValue = "$indent  + {$item['key']}: $newValue";
                $formattedOldValue = "$indent  - {$item['key']}: $oldValue";
                return "{$formattedOldValue}\n{$formattedNewValue}";
            case 'unchanged':
                $formattedValue = strigify($item['value'], $depth);
                return "$indent    {$item['key']}: $formattedValue";
            case 'nested':
                $children = $item['children'];
                $formattedHeader = "$indent    {$item['key']}: {";
                $body = stylish($children, $depth + 1);
                $formattedBody = implode("\n", $body);
                return "{$formattedHeader}\n{$formattedBody}\n{$indent}    }";
            default:
                throw new \Exception("Error, something wrong with {$item['type']}");
        }
    }, $tree);
    return $result;
}

function strigify($value, $depth)
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (!is_object($value)) {
        return (string) $value;
    }
    if (is_array($value)) {
        return implode(' ', $value);
    }

    $value = sortBy(get_object_vars($value), fn ($key) => $key, $sortFunction = 'ksort');
    $indent = makeIndent($depth);
    $stringedData = array_map(function ($key, $value) use ($depth, $indent) {
        $stringedData = strigify($value, $depth + 1);
        return "{$indent}    {$key}: $stringedData";
    }, array_keys($value), $value);
    $stringedData = implode("\n", $stringedData);
    return "{\n{$stringedData}\n{$indent}}";
}

function makeIndent($depth)
{
    return str_repeat('    ', $depth);
}
