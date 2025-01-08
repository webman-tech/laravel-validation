<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configs = [
    \WebmanTech\LaravelValidation\Facades\Validator::class => [
        'from' => \Illuminate\Support\Facades\Validator::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Validation\Factory',
            ' * @see \Illuminate\Support\Facades\Validator',
        ],
    ],
];

foreach ($configs as $targetClass => $config) {
    $methods = read_class_static_methods_from_comments($config['from']);

    $comments = [
        '/**'
    ];
    foreach ($methods as $info) {
        if (isset($config['change']['return'])) {
            $value = $info['return'];
            $info['return'] = $config['change']['return'][$value] ?? $value;
        }
        if (isset($config['change']['remove_fns']) && in_array($info['function'], $config['change']['remove_fns'], true)) {
            continue;
        }
        $comments[] = " * @method static {$info['return']} {$info['function_full']}";
    }

    $comments = array_merge($comments, $config['add'] ?? []);
    $comments[] = ' */';

    write_comments_to_class($targetClass, implode("\n", $comments));
    echo "Write comments to $targetClass\n";
}

function read_class_static_methods_from_comments(string $className): array
{
    $reflection = new ReflectionClass($className);
    $comments = $reflection->getDocComment();
    return collect(explode("\n", $comments))
        ->map(function ($comment) {
            $comment = trim($comment, "/* \t\n\r");
            if ($comment && str_starts_with($comment, '@method static')) {
                $comment = str_replace('@method static ', '', $comment);
                $return = explode(' ', $comment)[0];
                $functionFull = trim(str_replace($return, '', $comment));
                $function = explode('(', $functionFull)[0];
                return [
                    'return' => $return,
                    'function' => $function,
                    'function_full' => $functionFull,
                ];
            }
            return $comment;
        })
        ->filter(fn($comment) => is_array($comment))
        ->toArray();
}

function write_comments_to_class(string $className, string $comments): void
{
    $reflection = new ReflectionClass($className);
    $file = $reflection->getFileName();
    $oldComments = $reflection->getDocComment();
    $content = file_get_contents($file);
    $content = str_replace($oldComments, $comments, $content);
    file_put_contents($file, $content);
}
