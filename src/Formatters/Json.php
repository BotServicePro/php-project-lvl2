<?php

namespace Differ\Formatters\Json;

function render($tree)
{
    return json_encode($tree);
}
