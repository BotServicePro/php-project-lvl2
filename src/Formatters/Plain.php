<?php

namespace Differ\Formatters\Plain;

function plain($data, $path)
{
    $result = array_map(function ($item) use ($path) {
        switch ($item['type']) {
            case 'added':
                $stringedData = stringedData($item['value']);
                return 'Property ' . "'" . $path . $item['key'] . "'" . ' was added with value: ' . $stringedData;
            case 'removed':
                return 'Property ' . "'" . $path . $item['key'] . "'" . " was removed";
            case 'changed':
                $oldValue = stringedData($item['oldValue']);
                $newValue = stringedData($item['newValue']);
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

function stringedData($data)
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
    if (is_string($data) || is_double($data) || is_int($data)) {
        return "'" . $data . "'";
    }
    if (is_object($data)) {
        return "[complex value]";
    }
    return $data;
}
