<?php

namespace Differ\FileExtractor;

use Symfony\Component\Yaml\Yaml;

const JSONEXTENSION = -4;
const YMLEXTENSION = -3;
const YAMLEXTENSION = -4;

function extractData($fileName)
{
    if (substr($fileName, YMLEXTENSION) === 'yml') { // если это yml файлы
        $file = file_get_contents($fileName);
        $data = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    if (substr($fileName, YAMLEXTENSION) === 'yaml') { // если это yml файлы
        $file = file_get_contents($fileName);
        $data = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    if (substr($fileName, JSONEXTENSION) === 'json') { // если это json
        $file = file_get_contents($fileName);
        $data = json_decode($file);
    }
    return $data;
}
