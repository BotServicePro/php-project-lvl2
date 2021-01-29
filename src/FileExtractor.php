<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

const JSONEXTENSION = -4;
const YMLEXTENSION = -3;

function dataExtractor($fileName)
{
    if (substr($fileName, YMLEXTENSION) === 'yml') { // если это yml файлы
        $file = file_get_contents($fileName);
        $data = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    if (substr($fileName, JSONEXTENSION) === 'json') { // если это json
        $file = file_get_contents($fileName);
        $data = json_decode($file, $associative = true);
    }
    return $data;
}
