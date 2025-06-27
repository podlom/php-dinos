<?php

declare(strict_types=1);

require 'vendor/autoload.php';

// === Strategy Pattern ===

interface CodeFormatterStrategy
{
    public function format(string $language, string $code): string;
}

class ShortcodeFormatter implements CodeFormatterStrategy
{
    private array $shortcodeLangs;
    private array $langMap;

    public function __construct(array $shortcodeLangs, array $langMap)
    {
        $this->shortcodeLangs = $shortcodeLangs;
        $this->langMap = $langMap;
    }

    public function format(string $language, string $code): string
    {
        $lang = strtolower($language);
        $tag = $this->langMap[$lang] ?? $lang;

        if (in_array($lang, $this->shortcodeLangs, true)) {
            return sprintf('[%s]%s[/%s]', $tag, $code, $tag);
        }

        return sprintf('[code lang="%s"]%s[/code]', $tag, $code);
    }
}

class HtmlPreFormatter implements CodeFormatterStrategy
{
    public function format(string $language, string $code): string
    {
        return sprintf('<pre>%s</pre>', htmlspecialchars($code));
    }
}

class CodeFormatter
{
    private CodeFormatterStrategy $strategy;

    public function __construct(CodeFormatterStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function format(string $language, string $code): string
    {
        return $this->strategy->format($language, $code);
    }
}

// === Аргументи CLI ===

if ($argc < 2) {
    fwrite(STDERR, "Usage: php markdown-converter.php /path/to/file.md [--php-code-blocks=html|shortcode]\n");
    exit(1);
}

$filePath = $argv[1];
$formatterType = 'shortcode';

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--php-code-blocks=')) {
        $formatterType = explode('=', $arg)[1] ?? 'shortcode';
    }
}

if (!file_exists($filePath) || !is_readable($filePath)) {
    fwrite(STDERR, "Error: Cannot read file '$filePath'.\n");
    exit(1);
}

// === Ініціалізація парсера Markdown ===

$parsedown = new Parsedown();
$markdown = file_get_contents($filePath);
$html = $parsedown->text($markdown);

// === Вибір форматувальника ===

$shortcodeLangs = ['php', 'bash', 'shell', 'js', 'javascript', 'css', 'html'];
$langMap = ['javascript' => 'js', 'shell' => 'bash'];

$strategy = match ($formatterType) {
    'html' => new HtmlPreFormatter(),
    default => new ShortcodeFormatter($shortcodeLangs, $langMap),
};

$formatter = new CodeFormatter($strategy);

// === Обробка <pre><code class="language-xxx">...</code></pre> ===

$html = preg_replace_callback(
    '#<pre><code class="language-([a-z0-9\-\+]+)">(.*?)</code></pre>#si',
    function ($matches) use ($formatter) {
        $lang = strtolower($matches[1]);
        $code = htmlspecialchars_decode($matches[2], ENT_QUOTES);
        return $formatter->format($lang, trim($code));
    },
    $html
);

// === Обробка inline <code>...</code> (поза <pre>) ===

$html = preg_replace_callback(
    '#(?<!<pre>)<code>(.*?)</code>(?!</pre>)#si',
    function ($matches) {
        $code = htmlspecialchars_decode($matches[1], ENT_QUOTES);
        return sprintf('[code]%s[/code]', $code);
    },
    $html
);

// === Збереження результату ===

$outputFilePath = preg_replace('/\.md$/i', '.htm', $filePath);

if (file_put_contents($outputFilePath, $html) === false) {
    fwrite(STDERR, "Error: Failed to write to file '$outputFilePath'.\n");
    exit(1);
}

echo "✅ Conversion successful: '$filePath' -> '$outputFilePath'\n";
