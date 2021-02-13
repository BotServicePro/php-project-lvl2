<?php

namespace Differ\Formatters\Json;

function render($tree): string
{
    return json_encode($tree);
}
