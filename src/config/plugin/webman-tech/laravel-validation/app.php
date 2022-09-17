<?php

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use support\Container;

return [
    'enable' => true,
    'translation' => function () {
        return Container::get(TranslatorContract::class);
    }
];
