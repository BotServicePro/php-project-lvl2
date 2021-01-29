<?php

namespace Differ\Formatters\Json;

function render($data)
{
    print_r(json_encode($data));
    return json_encode($data);
}
