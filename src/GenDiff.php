<?php

namespace Differ\GenDiff;

use Exception;
use function Differ\Parser\parserIt;
use function Differ\Formatters\formateIt;

function genDiff($path1, $path2, $format) // главная функция
{
    if (!is_readable($path1) || !is_readable($path2)) {
        $path1 = $_SERVER['DOCUMENT_ROOT'] . '../' . $path1;
        $path2 = $_SERVER['DOCUMENT_ROOT'] . '../' . $path2;
    } elseif (!file_exists($path1) || !file_exists($path2)) {
        throw new Exception("First or second file not found.");
    }

    $firstFile = parserIt($path1); // получаем данные из файла в том виде в каком они есть
    $secondFile = parserIt($path2); // получаем данные из файла в том виде в каком они есть
    $differedData = diffData($firstFile, $secondFile); // получаем различия файлов и плоских и жирных
    $finalFormattedResult = formateIt($differedData, $format);
    print_r($finalFormattedResult);
    return $finalFormattedResult;
}

function diffData($firstFile, $secondFile)
{
    //$firstFile = json_decode(json_encode($firstFile), true);
    //$secondFile = json_decode(json_encode($secondFile), true);
    $firstFile = (array) $firstFile;
    $secondFile = (array) $secondFile;
    $uniqueKeys = array_keys(array_merge($firstFile, $secondFile));
    $data = array_map(function ($key) use ($firstFile, $secondFile) {
        // если текущий ключ не существует в первом файле, но присутствует во втором
        if (!array_key_exists($key, $firstFile)) {
            return ['key' => $key, 'value' => $secondFile[$key], 'type' => 'added'];
        // если текущего ключа нет во втором файле, (логично что он есть в первом)
        } elseif (!array_key_exists($key, $secondFile)) {
            return ['key' => $key, 'value' => $firstFile[$key], 'type' => 'removed'];
        }
        $nodeFirst = $firstFile[$key];
        $nodeSecond = $secondFile[$key];
        if (is_array($nodeFirst) === true && is_array($nodeSecond) === true) {
            $children = diffData($nodeFirst, $nodeSecond);
            // возможно придеться поменять  c type на status, что бы было легче перебирать в цикле
            $arr = ['key' => $key, 'type' => 'nested', 'children' => $children];
            usort($arr['children'], function ($a, $b) {
                return substr($a['key'], 0, 1) <=> substr($b['key'], 0, 1);
            });
            return $arr;
        }
        if ($nodeFirst === $nodeSecond) {
            return  ['key' => $key, 'value' => $nodeFirst, 'type' => 'unchanged'];
        } else {
            return ['key' => $key, 'oldValue' => $nodeFirst, 'newValue' => $nodeSecond, 'type' => 'changed'];
        }
    }, $uniqueKeys);
    //print_r($data);
    return $data;
}

//function isMultidimensional($data)
//{
//    // функция должна вернуть true или false если объект/массивы многомерные
//    foreach ($data as $item) {
//        foreach ($item as $subItem) {
//            if (is_array($subItem) === true || is_object($subItem) === true) {
//                return true;
//            }
//        }
//    }
//    return false;
//}

//function formatter($data)
//{
//    print_r($data);
//    if (isMultidimensional($data) === false) { // если одномерные данные
//        foreach ($data as $item) {
//            if ($item['status'] === 'changed') {
//                $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['oldValue'], true);
//                $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['newValue'], true);
//            }
//            if ($item['status'] === 'not changed') {
//                $tempResult[] = "    " . $item['key'] . ': ' . var_export($item['value'], true);
//            }
//            if ($item['status'] === 'added') {
//                $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['value'], true);
//            }
//            if ($item['status'] === 'removed') {
//                $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['value'], true);
//            }
//        }
//        usort($tempResult, function ($a, $b) {
//            return substr($a, STARTTOSORTFROMSYMBOL, 1) <=> substr($b, STARTTOSORTFROMSYMBOL, 1);
//        });
//        $tempResult = str_replace("'", '', $tempResult);
//        $finalResult = "{" . "\n" . implode("\n", $tempResult) . "\n" . "}";
//    } elseif (isMultidimensional($data) === true) { // если данные многомерные
//        //print_r($data);
//        foreach ($data as $item) {
//            if (isset($item['status']) && $item['status'] === 'added') {
//                  print_r($item['value']);
//            }
//            if (isset($item['status']) && $item['status'] === 'removed') {
//                print_r($item['value']);
//
//            }
//            if (isset($item['status']) && $item['status'] === 'not_changed') {
//                print_r($item['value']);
//            }
//            if (isset($item['type']) && $item['type'] === 'nested') {
//                print_r($item['children']);
//                //formatter($item['children']);
//            }
//            if (isset($item['status']) && $item['status'] === 'changed') {
//                print_r($item['oldValue']);
//                print_r($item['newValue']);
//            }
//        }
//    }
//    //print_r($tempResult);
//    return $finalResult;
//}




//function formatter ($data)
//{
    //print_r(json_encode($data));
//    print_r($data);
//    $tempResult = [];
//    foreach ($data as $item) {
//        if (isset($item['status']) && $item['status'] === 'changed') {
//            $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['oldValue'], true);
//            $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['newValue'], true);
//        }
//        if (isset($item['status']) && $item['status'] === 'not_changed') {
//            $tempResult[] = "    " . $item['key'] . ': ' . var_export($item['value'], true);
//        }
//        if (isset($item['status']) && $item['status'] === 'added') {
//            $tempResult[] = "  + " . $item['key'] . ': ' . var_export($item['value'], true);
//        }
//        if (isset($item['status']) && $item['status'] === 'removed') {
//            $tempResult[] = "  - " . $item['key'] . ': ' . var_export($item['value'], true);
//        }
//        if (isset($item['children'])) {
//            foreach ($item['children'] as $subItem) {
//                //print_r($subItem);
//
//                if (isset($subItem['status']) && $subItem['status'] === 'changed') {
//                    $tempResult[] = "  - " . $subItem['key'] . ': ' . var_export($subItem['oldValue'], true);
//                    $tempResult[] = "  + " . $subItem['key'] . ': ' . var_export($subItem['newValue'], true);
//                }
//                if (isset($subItem['status']) && $subItem['status'] === 'not_changed') {
//                    $tempResult[] = "    " . $subItem['key'] . ': ' . var_export($subItem['value'], true);
//                }
//                if (isset($subItem['status']) && $subItem['status'] === 'added') {
//                    $tempResult[] = "  + " . $subItem['key'] . ': ' . var_export($subItem['value'], true);
//                }
//                if (isset($subItem['status']) && $subItem['status'] === 'removed') {
//                    $tempResult[] = "  - " . $subItem['key'] . ': ' . var_export($subItem['value'], true);
//                }
//
//
//            }
//
//        }
//    }
//
//    print_r($tempResult);





//    $result = array_map(function ($item) {
//        foreach ($item as $value) {
//            if (isset($value['status']) && $value['status'] === 'changed') {
//                $tempResult[] = "  - " . $value['key'] . ': ' . var_export($value['oldValue'], true);
//                $tempResult[] = "  + " . $value['key'] . ': ' . var_export($value['newValue'], true);
//            }
//            if (isset($value['status']) && $value['status'] === 'not_changed') {
//                $tempResult[] = "    " . $value['key'] . ': ' . var_export($value['value'], true);
//            }
//            if (isset($value['status']) && $value['status'] === 'added') {
//                $tempResult[] = "  + " . $value['key'] . ': ' . var_export($value['value'], true);
//            }
//            if (isset($value['status']) && $value['status'] === 'removed') {
//                $tempResult[] = "  - " . $value['key'] . ': ' . var_export($value['value'], true);
//            }
//            if (isset($value['children'])) {
//                foreach ($value['children'] as $subItem) {
//                    //print_r($subItem);
//                }
//
//            }
//    }
//
//    }, $data);
//    print_r($result);
//    return $result;
//}



