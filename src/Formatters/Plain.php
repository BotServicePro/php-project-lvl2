<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

function render($data)
{
    $stringedData = plain($data, '');
    $formatedData = implode("\n", flattenAll($stringedData));
    print_r($formatedData);
    return $formatedData;
}

function plain($data, $path)
{
    $result = array_map(function ($item) use ($path) {
        switch ($item['type']) {
            case 'added':
                $stringedData = convertToString($item['value']);
                return 'Property ' . "'" . $path . $item['key'] . "'" . ' was added with value: ' . $stringedData;
            case 'removed':
                return 'Property ' . "'" . $path . $item['key'] . "'" . " was removed";
            case 'changed':
                $oldValue = convertToString($item['oldValue']);
                $newValue = convertToString($item['newValue']);
                return 'Property ' . "'" . $path . $item['key'] . "'" .
                    " was updated. From " . $oldValue . ' to ' . $newValue;
            case 'unchanged':
                return [];
            case 'nested':
                $nestedPath = $path . $item['key'] . '.';
                $children = $item['children'];
                return plain($children, $nestedPath);
        }
    }, $data);
    return $result;
}

function convertToString($data)
{
    if ($data === null || is_bool($data)) {
        return strtolower(var_export($data, true));
    } elseif (is_array($data)) {
        return "[complex value]";
    } elseif (is_string($data) || is_double($data) || is_int($data)) {
        return "'" . $data . "'";
    } elseif (is_object($data)) {
        return "[complex value]";
    }
    return $data;
}
