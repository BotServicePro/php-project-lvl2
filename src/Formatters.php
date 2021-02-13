<?php

namespace Differ\Formatters;

function format($tree, $format): string
{
    switch ($format) {
        case 'stylish':
            return Stylish\render($tree);
        case 'plain':
            return Plain\render($tree);
        case 'json':
            return Json\render($tree);
        default:
            throw new \Exception("Error, wrong format $format!");
    }
}
