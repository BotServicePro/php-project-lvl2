<?php

namespace Differ\Differ;

use Exception;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Differ\TreeBuilder\buildTree;

function readFile($path): string
{
    if (!file_exists($path)) {
        throw new Exception("File not found. Wrong filepath is: $path");
    }
    return file_get_contents($path);
}

function getExtension($path)
{
    return pathinfo($path, PATHINFO_EXTENSION);
}

function genDiff($path1, $path2, $format = 'stylish'): string
{
    $firstPath = readFile($path1);
    $secondPath = readFile($path2);
    $firstData = parse($firstPath, getExtension($path1));
    $secondData = parse($secondPath, getExtension($path2));
    $differedTree = buildTree($firstData, $secondData);
    return format($differedTree, $format);
}
