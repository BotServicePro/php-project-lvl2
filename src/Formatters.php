<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\render as stylish;
use function Differ\Formatters\Plain\render as plain;
use function Differ\Formatters\Json\render as json;

function formateIt($data, $type)
{
    if ($type === 'stylish') {
        return stylish($data);
    } elseif ($type === 'plain') {
        return plain($data);
    } elseif ($type === 'json') {
        return json($data);
    }
}
