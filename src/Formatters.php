<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\render as stylish;
use function Differ\Formatters\Plain\render as plain;
use function Differ\Formatters\Json\render as json;

function astToStringConverter($data, $type)
{
    switch ($type) {
        case 'stylish':
            return stylish($data);
        case 'plain':
            return plain($data);
        case 'json':
            return json($data);
    }
}
