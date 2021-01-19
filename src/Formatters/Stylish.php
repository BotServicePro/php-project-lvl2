<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;

const STARTTOSORTFROMSYMBOL = 4;

function convertToString ($data) {
    if ($data === null) {
        return 'null';
    } elseif (is_bool($data) === true) {
        return var_export($data, true);
    } elseif (is_string($data) === true) {
        return $data;
    } elseif (!is_string($data) && is_object($data)) {
        return $data;
    }

    //$convertedToString = array_map();
}


function stylish($data)
{
        print_r($data);
        $tempData = array_map(function ($item) {

            $plus = "  + ";
            $minus = "  - ";
            $space = "    ";
            $doublePoint = ": ";

            switch ($item['type']) {
                case 'added':


                    return $plus . $item['key'] . $doublePoint . var_export($item['value'], true);
                case 'removed':


                    return $minus . $item['key'] . $doublePoint . var_export($item['value'], true);
                case 'changed':


                    $oldNewValue[] = $tempResult[] = $minus . $item['key'] . $doublePoint
                        . var_export($item['oldValue'], true);
                    $oldNewValue[] = $tempResult[] = $plus . $item['key'] . $doublePoint
                        . var_export($item['newValue'], true);
                    return $oldNewValue;
                case 'unchanged':


                    return $space . $item['key'] . $doublePoint . var_export($item['value'], true);
                case 'nested':


                    return stylish($item['children']);
            }
        }, $data);
        echo '===============================';
        print_r($tempData);

        $tempData = flattenAll($tempData);
        usort($tempData, function ($a, $b) {
            return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
        });
        $finalResult = str_replace("'", '', $tempData);
        return "{" . "\n" . implode("\n", $finalResult) . "\n" . "}";
}


// ПОЛНОСТЬЮ РАБОТАЕТ С ПЛОСКИМИ ДАННЫМИ
//function stylish($data)
//{
//    print_r($data);
//
//    $tempData = array_map(function ($item) {
//        switch ($item['type']) {
//            case 'added':
//                return "  + " . $item['key'] . ': ' . var_export($item['value'], true);
//            case 'removed':
//                return "  - " . $item['key'] . ': ' . var_export($item['value'], true);
//            case 'changed':
//                $oldNewValue[] = $tempResult[] = "  - " . $item['key'] . ': '
//                    . var_export($item['oldValue'], true);
//                $oldNewValue[] = $tempResult[] = "  + " . $item['key'] . ': '
//                    . var_export($item['newValue'], true);
//                return $oldNewValue;
//            case 'unchanged':
//                return "    " . $item['key'] . ': ' . var_export($item['value'], true);
//            case 'nested':
//                return stylish($item['children']);
//        }
//    }, $data);
//    echo '===============================';
//    print_r($tempData);
//
//    $tempData = flattenAll($tempData);
//    usort($tempData, function ($a, $b) {
//        return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
//    });
//    $finalResult = str_replace("'", '', $tempData);
//    return "{" . "\n" . implode("\n", $finalResult) . "\n" . "}";
//}