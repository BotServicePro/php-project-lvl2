<?php

namespace Differ\Formatters\Stylish;

function stylish($data, $depth)
{
    $result = array_map(function ($item) use ($depth) {
        $tabulation = str_repeat('    ', $depth - 1);
        switch ($item['type']) {
            case 'added':
//                if (is_object($item['value'])) {
//                    print_r($item['value']);
//                }
                $stringedData = stringedData($item['value'], $depth);
                return "$tabulation  + {$item['key']}: $stringedData";
            case 'removed':
                $stringedData = stringedData($item['value'], $depth);
                return "$tabulation  - {$item['key']}: $stringedData";
            case 'changed':
                $oldValue = stringedData($item['oldValue'], $depth);
                $newValue = stringedData($item['newValue'], $depth);
                $stringedNewValue = "$tabulation  + {$item['key']}: $newValue";
                $stringedOldValue = "$tabulation  - {$item['key']}: $oldValue";
                return $stringedOldValue . "\n" . $stringedNewValue;
            case 'unchanged':
                $stringedData = stringedData($item['value'], $depth);
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

function stringedData($data, $depth)
{
    if (is_object($data)) {
        $data = (array) $data;
//        $keys = array_keys($data);
//        $sortedKeys = sort($keys, SORT_NATURAL);
//        $sortedResult = array_map(function ($value) {
//            print_r($value);
//            echo '-----';
//        }, $data);
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
            $stringedValue = stringedData($value, $depth + 1);
            return "{$tabulation}{$space}{$key}: $stringedValue";
        }
        if (!is_array($value) && !is_object($value)) {
            return "{$tabulation}{$space}{$key}: $value";
        }
        if (is_array($value) && is_array($value[key($value)])) {
            $stringed = stringedData($value, $depth + 1);
            return "{$tabulation}{$space}{$key}: $stringed";
        }
        if (is_array($value) && !is_array($value[key($value)])) {
            $stringed2 = stringedData($value, $depth + 1);
            return "{$string}\n{$tabulation}{$space}{$key}: $stringed2";
        }
    }, array_keys($data), $data);
    $stringedData = implode("\n", $stringedData);
    return "{\n{$stringedData}\n{$tabulation}}";
}
