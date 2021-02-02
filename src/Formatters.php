<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\render as json;
use function Funct\Collection\flatten;
use function Funct\Collection\flattenAll;

function astToStringConverter($data, $type)
{
    switch ($type) {
        case 'stylish':
            $depth = 1;
            $stringedTree = stylish($data, $depth);
            $finalResult = '{' . "\n" . implode("\n", flatten($stringedTree))  . "\n" . '}';
            $finalResult = str_replace("'", '', $finalResult);
            print_r($finalResult);
            return $finalResult;
        case 'plain':
            $stringedData = plain($data, '');
            $formatedData = implode("\n", flattenAll($stringedData));
            print_r($formatedData);
            return $formatedData;
        case 'json':
            print_r(json($data));
            return json($data);
    }
}
