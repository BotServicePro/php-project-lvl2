<?php

namespace Differ\Formatters;

function format($tree, $format)
{
    switch ($format) {
        case 'stylish':
            print_r(Stylish\render($tree));
            return Stylish\render($tree);
        case 'plain':
            print_r(Plain\render($tree));
            return Plain\render($tree);
        case 'json':
            print_r(Json\render($tree));
            return Json\render($tree);
        default:
            throw new \Exception("Error, wrong format!");
    }
}
