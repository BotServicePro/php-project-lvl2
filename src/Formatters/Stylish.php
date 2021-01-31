<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flatten;

function render($data)
{
    $depth = 1;
    $stringedTree = stylish($data, $depth);
    $finalResult = '{' . "\n" . implode("\n", flatten($stringedTree))  . "\n" . '}';
    $finalResult = str_replace("'", '', $finalResult);
    print_r($finalResult);
    return $finalResult;
}

function stylish($data, $depth)
{
    $result = array_map(function ($item) use ($depth) {
        $plus = "  + ";
        $minus = "  - ";
        $space = "    ";
        $doublePoint = ": ";
        $tabulation = str_repeat('    ', $depth - 1);

        switch ($item['type']) {
            case 'added':
                $stringedData = convertToString($item['value'], $depth);
                return $tabulation . $plus . $item['key'] . $doublePoint . $stringedData;
            case 'removed':
                $stringedData = convertToString($item['value'], $depth);
                return $tabulation . $minus . $item['key'] . $doublePoint . $stringedData;
            case 'changed':
                //конвертим старое значение
                $oldValue = convertToString($item['oldValue'], $depth);
                //конвертим новоe значние
                $newValue = convertToString($item['newValue'], $depth);
                // записываем в переменне с форматирование
                $stringedNewValue = $tabulation . $plus . $item['key'] . $doublePoint . $newValue;
                $stringedOldValue = $tabulation . $minus . $item['key'] . $doublePoint . $oldValue;
                // возвращаем две новые строки
                return $stringedOldValue . "\n" . $stringedNewValue;
            case 'unchanged':
                $stringedData = convertToString($item['value'], $depth);
                return $tabulation . $space . $item['key'] . $doublePoint . $stringedData;
            case 'nested':
                // получаем то что вложено
                $children = $item['children'];
                // делаем первую строку с ключом и отступом
                $stringedHeader = $tabulation . $space . $item['key'] . $doublePoint .  '{';
                // тело данных, вызываем функцию рекурсивно с вложенными данными
                $body = stylish($children, $depth + 1);
                // пересобираем тело с новой строки
                $stringedBody = implode("\n", $body);
                return $stringedHeader . "\n" . $stringedBody . "\n" . $tabulation . $space . '}';
        }
    }, $data);

    return $result;
}

function convertToString($data, $depth)
{
    if ($data === null || is_bool($data)) {
        return strtolower(var_export($data, true));
    } elseif (is_string($data) || is_double($data) || is_int($data)) {
        return var_export($data, true);
    } elseif (!is_array($data)) {
        return $data;
    }
    $space = '    ';
    $tabulation = str_repeat($space, $depth);
    $string = '';

    $stringedData = array_map(function ($key, $value) use ($depth, $tabulation, $space, $string) {
        if (!is_array($value)) {
            return $tabulation . $space . $key . ': ' . $value;
        }
        if (is_array($value) && is_array($value[key($value)])) {
            $stringed = convertToString($value, $depth + 1);
            return $tabulation . $space . $key . ': ' . $stringed;
        }
        if (is_array($value) && !is_array($value[key($value)])) {
            $stringed2 = convertToString($value, $depth + 1);
            $string = $string . "\n" . $tabulation . $space . $key . ': ' . $stringed2;
            return preg_replace('/^\h*\v+/m', '', $string); // удаляем пустые строки
        }
    }, array_keys($data), $data);
    $stringedData = implode("\n", $stringedData);
    return "{" . "\n" . $stringedData . "\n" . $tabulation . "}";
}
