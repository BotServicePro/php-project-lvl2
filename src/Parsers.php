<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $extension): object
{
    switch ($extension) {
        case 'yaml':
        case 'yml':
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        case 'json':
            return json_decode($data);
        default:
            throw new \Exception("Unknown file extension $extension!");
    }
}
