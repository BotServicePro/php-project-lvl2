<?php

namespace Differ\Formatters\Json;

function render($tree): string
{
    return (string) json_encode($tree);
}
