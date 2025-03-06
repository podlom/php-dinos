<?php

declare(strict_types=1);

function showTime(string $time): string
{
    $time = (int) $time;
    echo 'Displaying date time for Unix time stamp: ' . $time . PHP_EOL;

    $date = new DateTime();
    $date->setTimestamp($time);
    $date->setTimezone(new DateTimeZone('Europe/Kyiv'));

    $formattedDate = $date->format('Y-m-d H:i:s');
    return $formattedDate . PHP_EOL;
}


if (isset($argv[1])
    && (strlen($argv[1]) > 0)
) {
    echo showTime($argv[1]) . PHP_EOL;
} else {
    echo "Script usage: php " . $argv[0] . " UnixTimeStamp\n";
}