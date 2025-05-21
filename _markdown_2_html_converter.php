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
$markdown = file_get_contents($filePath);
$html = $parsedown->text($markdown);

// Мови, які підтримують короткий формат [lang]...[/lang]
$shortcodeLangs = ['php', 'bash', 'shell', 'js', 'javascript', 'css', 'html'];

// Псевдоніми → базові шорткоди
$langMap = [
    'javascript' => 'js',
    'shell' => 'bash',
];

// Обробка блоків коду <pre><code class="language-xxx">...</code></pre>
$html = preg_replace_callback(
    '#<pre><code class="language-([a-z0-9\-\+]+)">(.*?)</code></pre>#si',
    function ($matches) use ($shortcodeLangs, $langMap) {
        $lang = strtolower($matches[1]);
        $code = htmlspecialchars_decode($matches[2], ENT_QUOTES);
        $code = trim($code);

        $tag = $langMap[$lang] ?? $lang;

        if (in_array($lang, $shortcodeLangs, true)) {
            return sprintf('[%s]%s[/%s]', $tag, $code, $tag);
        }

        return sprintf('[code lang="%s"]%s[/code]', $tag, $code);
    },
    $html
);

// Обробка inline-коду: <code>...</code> → [code]...[/code], але ігноруємо ті, що вже всередині <pre>
$html = preg_replace_callback(
    '#(?<!<pre>)<code>(.*?)</code>(?!</pre>)#si',
    function ($matches) {
        $code = htmlspecialchars_decode($matches[1], ENT_QUOTES);
        return sprintf('[code]%s[/code]', $code);
    },
    $html
);

// Збереження результату
$outputFilePath = preg_replace('/\.md$/i', '.htm', $filePath);

if (file_put_contents($outputFilePath, $html) === false) {
    fwrite(STDERR, "Error: Failed to write to file '$outputFilePath'.\n");
    exit(1);
}

echo "Conversion successful: '$filePath' -> '$outputFilePath'\n";
