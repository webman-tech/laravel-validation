<?php

use Illuminate\Contracts\Validation\Factory as ValidationFactory;

return [
    'enable' => true,
    /**
     * 扩展自定义验证规则
     */
    'extends' => function (ValidationFactory $validator): void {
        //$validator->extend();
    }
];
