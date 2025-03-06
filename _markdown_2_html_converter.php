<?php

declare(strict_types=1);

require 'vendor/autoload.php';

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php markdown-converter.php /path/to/file.md\n");
    exit(1);
}

$filePath = $argv[1];

if (!file_exists($filePath) || !is_readable($filePath)) {
    fwrite(STDERR, "Error: Cannot read file '$filePath'.\n");
    exit(1);
}

$parsedown = new Parsedown();

// Читаємо вміст Markdown-файлу
$markdown = file_get_contents($filePath);

// Конвертуємо Markdown у HTML
$html = $parsedown->text($markdown);

// Формуємо шлях до вихідного файлу з розширенням .htm
$outputFilePath = preg_replace('/\.md$/i', '.htm', $filePath);

if (file_put_contents($outputFilePath, $html) === false) {
    fwrite(STDERR, "Error: Failed to write to file '$outputFilePath'.\n");
    exit(1);
}

echo "Conversion successful: '$filePath' -> '$outputFilePath'\n";

