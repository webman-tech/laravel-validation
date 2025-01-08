<?php
/**
 * 从 https://github.com/laravel/framework 源码读取 en 的 validation 文件
 * 从 https://github.com/Laravel-Lang/lang 读取翻译文件
 */

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\VarExporter\VarExporter;

$basePath = __DIR__ . '/../src/copy/resource/translations';
$enLink = 'https://raw.githubusercontent.com/laravel/framework/refs/heads/11.x/src/Illuminate/Translation/lang/en/validation.php';

echo "Getting en validation contents...\n";
$content = file_get_contents($enLink);
$replace = <<<PHP
<?php
/**
 * @link {$enLink}
 */
PHP;
$content = str_replace("<?php", $replace, $content);
file_put_contents($basePath. '/en/validation.php', $content);
echo 'Done';
$enValidation = require $basePath. '/en/validation.php';

$extraLang = ['zh_CN'];
foreach ($extraLang as $lang) {
    echo "Getting {$lang} validation contents...\n";
    $link = "https://raw.githubusercontent.com/Laravel-Lang/lang/refs/heads/main/locales/{$lang}/php.json";
    $data = json_decode(file_get_contents($link), true);
    $langValidation = deep_replace_array_value($enValidation, $data);
    file_put_contents($basePath. "/{$lang}/validation.php", export_php_array($langValidation, $link));
    echo 'Done';
}

function deep_replace_array_value(array $base, array $target, string $prefix = ''): array
{
    foreach ($base as $key => $value) {
        if (is_array($value)) {
            $base[$key] = deep_replace_array_value($value, $target, $key . '.');
        } else {
            $base[$key] = $target[$prefix . $key] ?? $value;
        }
    }
    return $base;
}

function export_php_array(array $data, string $link): string
{
    $content = VarExporter::export($data);
    return <<<PHP
<?php
/**
 * @link {$link}
 */

return {$content};

PHP;
}
