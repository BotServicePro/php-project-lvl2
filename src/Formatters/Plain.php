<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

const STARTTOSORTFROMSYMBOL = 4;

function plain($data)
{

    // если содержимое НЕ ОБъЕКТ И НЕ МАССИВ, возвращаем ключ
    if (is_array($data) === false && is_object($data) === false) {
        return [$data['key']];
    }


    // попробуем собрать полный путь до файлов
    $onlyPath = array_reduce($data, function ($acc, $item) {
        //print_r($item);

        if ($item['type'] === 'nested') {
            return $acc[$item['key']];
        }

    }, []);
    print_r($onlyPath);





}