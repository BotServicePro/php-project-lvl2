<?php

namespace Differ\Formatters\Json;

function render($data)
{
    // без сортировки
    print_r(json_encode($data));
    return json_encode($data);
}
