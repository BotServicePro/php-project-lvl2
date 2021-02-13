<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flatten;
use function Funct\Collection\sortBy;

function render($tree): string
{
    $formattedTree = stylish($tree);
    $tempResult = "{\n" . implode("\n", flatten($formattedTree)) . "\n}";
    $finalResult = str_replace("'", '', $tempResult);
    return $finalResult;
}

function stylish($tree, $depth = 1): array
{
    return array_map(function ($item) use ($depth): string {
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
                throw new \Exception("Error, could not identify 'type' in {$item}");
        }
    }, $tree);
}

function strigify($value, $depth): string
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
    $sortedValue = sortBy(get_object_vars($value), fn ($key) => $key, $sortFunction = 'ksort');
    $indent = makeIndent($depth);
    $formettedValue = array_map(function ($key, $value) use ($depth, $indent): string  {
        $formattedValue = strigify($value, $depth + 1);
        return "{$indent}    {$key}: $formattedValue";
    }, array_keys($sortedValue), $sortedValue);
    $result = implode("\n", $formettedValue);
    return "{\n{$result}\n{$indent}}";
}

function makeIndent($depth): string
{
    return str_repeat('    ', $depth);
}
