<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;

function formateIt($data, $type)
{
    if ($type === 'stylish') {
        return stylish($data);
    } elseif ($type === 'plain') {
        return plain($data);
    }
}
