<?php

function binarySearch(array $list, int $item): int|null
{
    $low = 0;
    $high = count($list) - 1;

    do {
        $mid = ($low + $high) / 2;
        $guess = $list[$mid];
        if ($guess == $item) {
            return $mid;
        }
        if ($guess > $item) {
            $high = $mid - 1;
        } else {
            $low = $mid + 1;
        }
    } while ($low <= $high);

    return null;
}

$myList = [1, 3, 5, 7, 9, 11, 13, 15];

echo 'binarySearch 11: ' . var_export(binarySearch($myList, 11), true) . PHP_EOL;
echo 'binarySearch -1: ' . var_export(binarySearch($myList, -1), true) . PHP_EOL;