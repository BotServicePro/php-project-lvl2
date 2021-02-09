<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\render as stylish;
use function Differ\Formatters\Plain\render as plain;
use function Differ\Formatters\Json\render as json;

function format($tree, $format)
{
    switch ($format) {
        case 'stylish':
            print_r(stylish($tree));
            return stylish($tree);
        case 'plain':
            print_r(plain($tree));
            return plain($tree);
        case 'json':
            print_r(json($tree));
            return json($tree);
        default:
            echo 'Unknown format';
    }
}
