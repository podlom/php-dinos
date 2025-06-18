<?php

/**
 * date_shift.php
 *
 * 📅 Simple CLI tool to calculate a new date by shifting a given date by a specified number of days.
 *
 * Usage:
 *   php date_shift.php 2025-04-06 + 90
 *   php date_shift.php 2025-04-06 - 30
 *
 * Arguments:
 *   1. Input date (YYYY-MM-DD)
 *   2. Operator: '+' or '-'
 *   3. Number of days to shift (optional, default is 90)
 *
 * Features:
 *   - Validates input date format (YYYY-MM-DD)
 *   - Validates operator (+ or -)
 *   - Handles default values when arguments are omitted
 *
 * Author: Taras Shkodenko <taras.shkodenko@gmail.com>
 * License: MIT
 * Date: 2025-06-18
 */

function getNewDate(string $inputDate = '2025-04-06', string $plusOrMinus = '+', int $numDays = 90): void
{
    // Перевірка на коректність дати
    $date = DateTime::createFromFormat('Y-m-d', $inputDate);
    if (! $date || $date->format('Y-m-d') !== $inputDate) {
        echo "❌ Error: Invalid input date format. Use YYYY-MM-DD.\n";
        return;
    }

    // Перевірка на коректність оператора
    if (! in_array($plusOrMinus, ['+', '-'])) {
        echo "❌ Error: Invalid operator. Use '+' or '-'.\n";
        return;
    }

    $date->modify("{$plusOrMinus}{$numDays} days");

    echo "📅 Input Date: {$inputDate}\n";
    echo "➡️ Date after {$plusOrMinus}{$numDays} days: " . $date->format('Y-m-d') . "\n";
}

$scriptName = $argv[0] ?? 'script.php';
$argsCount = $argc - 1;

switch ($argsCount) {
    case 3:
        getNewDate($argv[1], $argv[2], (int)$argv[3]);
        break;
    case 2:
        getNewDate($argv[1], $argv[2]);
        break;
    case 1:
        getNewDate($argv[1]);
        break;
    default:
        echo "📌 Usage: php {$scriptName} YYYY-MM-DD [+|-] [days]\n";
        echo "📌 Example: php {$scriptName} 2025-04-06 + 90\n";
}
