<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flatten;

function render($tree)
{
    $depth = 1;
    $stringedTree = stylish($tree, $depth);
    $finalResult = '{' . "\n" . implode("\n", flatten($stringedTree))  . "\n" . '}';
    $finalResult = str_replace("'", '', $finalResult);
    return $finalResult;
}

function stylish($data, $depth)
{
    $result = array_map(function ($item) use ($depth) {
        $tabulation = str_repeat('    ', $depth - 1);
        switch ($item['type']) {
            case 'added':
                $stringedData = strigify($item['value'], $depth);
                return "$tabulation  + {$item['key']}: $stringedData";
            case 'removed':
                $stringedData = strigify($item['value'], $depth);
                return "$tabulation  - {$item['key']}: $stringedData";
            case 'changed':
                $oldValue = strigify($item['oldValue'], $depth);
                $newValue = strigify($item['newValue'], $depth);
                $stringedNewValue = "$tabulation  + {$item['key']}: $newValue";
                $stringedOldValue = "$tabulation  - {$item['key']}: $oldValue";
                return $stringedOldValue . "\n" . $stringedNewValue;
            case 'unchanged':
                $stringedData = strigify($item['value'], $depth);
                return "$tabulation    {$item['key']}: $stringedData";
            case 'nested':
                $children = $item['children'];
                $stringedHeader = "$tabulation    {$item['key']}: {";
                $body = stylish($children, $depth + 1);
                $stringedBody = implode("\n", $body);
                return "{$stringedHeader}\n{$stringedBody}\n{$tabulation}    }";
        }
    }, $data);
    return $result;
}

function strigify($data, $depth)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if ($data === null || is_bool($data)) {
        return strtolower(var_export($data, true));
    }
    if (is_string($data) || is_double($data) || is_int($data)) {
        return var_export($data, true);
    }
    if (!is_array($data)) {
        return var_export($data, true);
    }
    $space = '    ';
    $tabulation = str_repeat($space, $depth);
    $string = '';
    $stringedData = array_map(function ($key, $value) use ($depth, $space, $tabulation, $string) {
        if (is_object($value)) {
            $value = (array) $value;
            $stringedValue = strigify($value, $depth + 1);
            return "{$tabulation}{$space}{$key}: $stringedValue";
        }
        if (!is_array($value) && !is_object($value)) {
            return "{$tabulation}{$space}{$key}: $value";
        }
        if (is_array($value) && is_array($value[key($value)])) {
            $stringed = strigify($value, $depth + 1);
            return "{$tabulation}{$space}{$key}: $stringed";
        }
        if (is_array($value) && !is_array($value[key($value)])) {
            $stringed2 = strigify($value, $depth + 1);
            return "{$string}\n{$tabulation}{$space}{$key}: $stringed2";
        }
    }, array_keys($data), $data);
    $stringedData = implode("\n", $stringedData);
    return "{\n{$stringedData}\n{$tabulation}}";
}
