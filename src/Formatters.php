<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\render;
use function Differ\Formatters\Plain\plain;

function formateIt($data, $type)
{
    if ($type === 'stylish') {
        return render($data);
    } elseif ($type === 'plain') {
        return plain($data);
    }
}
