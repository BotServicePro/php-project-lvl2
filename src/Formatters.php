<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\render as stylish;
use function Differ\Formatters\Plain\render as plain;

function formateIt($data, $type)
{
    if ($type === 'stylish') {
        return stylish($data);
    } elseif ($type === 'plain') {
        return plain($data);
    }
}
