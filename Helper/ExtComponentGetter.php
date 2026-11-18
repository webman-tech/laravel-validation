<?php

namespace WebmanTech\LaravelValidation\Helper;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\DatabasePresenceVerifierInterface;
use Illuminate\Validation\ValidationServiceProvider;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use WebmanTech\LaravelDatabase\Facades\DB;
use WebmanTech\LaravelTranslation\Facades\Translator;
use WebmanTech\LaravelValidation\Translation\NullTranslator;
use WebmanTech\LaravelValidation\Translation\WebmanSymfonyTranslator;

/**
 * @internal
 */
final class ExtComponentGetter extends BaseExtComponentGetter
{
    protected static function getDefine(): array
    {
        return [
            /** @see TranslationServiceProvider::register() */
            TranslatorContract::class => [
                'alias' => ['translator'],
                'singleton' => function () {
                    if (class_exists(Translator::class)) {
                        return Translator::instance();
                    }
                    if (class_exists(SymfonyTranslator::class)) {
                        return new WebmanSymfonyTranslator();
                    }
                    return new NullTranslator();
                },
            ],
            /** @see DatabaseServiceProvider::registerConnectionServices() */
            ConnectionResolverInterface::class => [
                'alias' => ['db'],
                // laravel-database 为可选依赖，未安装时返回 null（unique 规则使用时才需要）
                'singleton' => fn() => class_exists(DB::class) ? DB::getInstance()->getDatabaseManager() : null,
            ],
            /** @see ValidationServiceProvider::registerPresenceVerifier() */
            DatabasePresenceVerifierInterface::class => [
                'alias' => ['validation.presence'],
                'singleton' => function () {
                    $db = self::get(ConnectionResolverInterface::class);
                    if (!$db instanceof ConnectionResolverInterface) {
                        return null;
                    }
                    return new DatabasePresenceVerifier($db);
                }
            ],
        ];
    }
}
