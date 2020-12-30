<?php

namespace Differ\Parsers;

function parser($firstFile, $secondFile)
{
    $wasDeletedTemp = array_diff_key($firstFile, $secondFile); // то что было удалено из первого
    foreach ($wasDeletedTemp as $key => $value) {
        $wasDeleted[] = '  - ' . $key . ': ' . var_export($value, true); // var_export фактический результат
    }

    $wasNotChangedTemp = array_intersect_assoc($firstFile, $secondFile);
    foreach ($wasNotChangedTemp as $key => $value) {
        $wasNotChanged[] = '    ' . $key . ': ' . var_export($value, true);
    }

    $wasAddedTemp = array_diff_key($secondFile, $firstFile); // то что добавилось во второй массив
    foreach ($wasAddedTemp as $key => $value) {
        $wasAdded[] = '  + ' . $key . ': ' . var_export($value, true);
    }

    foreach ($firstFile as $key => $value) {
        if (array_key_exists($key, $secondFile) === true && $firstFile[$key] !== $secondFile[$key]) {
            $wasChanged[] = ['  - ' . $key .  ': ' . $firstFile[$key],
                '  + ' . $key . ': ' . $secondFile[$key]];
        }
    }
    return array_merge($wasDeleted, $wasNotChanged, $wasAdded, $wasChanged);
}
