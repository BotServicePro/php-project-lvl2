<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($fileData, $extension): object
{
    switch ($extension) {
        case 'yaml':
        case 'yml':
            return Yaml::parse($fileData, Yaml::PARSE_OBJECT_FOR_MAP);
        case 'json':
            return json_decode($fileData);
        default:
            throw new \Exception("Unknown file extension $extension!");
    }
}
