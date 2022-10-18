<?php

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

return [
    'enable' => true,
    /**
     * translation 实例
     */
    'translation' => function (): ?Translator {
        return null;
    },
    /**
     * 扩展自定义验证规则
     */
    'extends' => function (ValidationFactory $validator): void {
        //$validator->extend();
    }
];
