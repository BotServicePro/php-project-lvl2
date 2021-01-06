<?php

namespace Differ\Parsers;

function parser($firstFile, $secondFile) // рабочий парсер плоских данных
{
    $firstFile = json_decode(json_encode($firstFile), true);
    $secondFile = json_decode(json_encode($secondFile), true);

    $wasDeletedTemp = array_diff_key($firstFile, $secondFile); // то что было удалено из первого массива
    foreach ($wasDeletedTemp as $key => $value) {
        $wasDeleted[] = '  - ' . $key . ': ' . var_export($value, true); // var_export фактический результат
    }

    $wasNotChangedTemp = array_intersect_assoc($firstFile, $secondFile); // то что не изменилось
    foreach ($wasNotChangedTemp as $key => $value) {
        $wasNotChanged[] = '    ' . $key . ': ' . var_export($value, true);
    }

    $wasAddedTemp = array_diff_key($secondFile, $firstFile); // то что добавилось во второй массив
    foreach ($wasAddedTemp as $key => $value) {
        $wasAdded[] = '  + ' . $key . ': ' . var_export($value, true);
    }

    foreach ($firstFile as $key => $value) { // то что было изменено
        if (array_key_exists($key, $secondFile) === true && $firstFile[$key] !== $secondFile[$key]) {
            $wasChanged[] = ['  - ' . $key .  ': ' . $firstFile[$key],
                '  + ' . $key . ': ' . $secondFile[$key]];
        }
    }
    return array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged); // объединяем
}



function recursiveParser($firstFile, $secondFile) // парсер многомерных данных
{
    // приводим к общему типу
    $firstFile = json_decode(json_encode($firstFile), true);
    $secondFile = json_decode(json_encode($secondFile), true);
    print_r($firstFile);

    $removed = $added = $nested = $unchanged = $changed = [];

    $result = [];
    function buildTree($firstFile)
    {
        // пока просто рекурсивная печать всех данных из первого файла
        foreach ($firstFile as $firstKey => $firstItem) {
            if (is_array($firstItem) === false) {
                print_r($firstItem . ' ');
            }
            if (is_array($firstItem) === true) {
                buildTree($firstItem);
            }
        }
    }
    buildTree($firstFile);

    //return parser($firstFile, $secondFile);
}
