<?php

use support\Container;

return [
    'enable' => true,
    'translation' => function () {
        // 不处理验证消息提示，需要自定义消息类型
        return Container::get(\WebmanTech\LaravelValidation\Translation\NullTranslator::class);
        // 实现过 Translator 的，可以使用类似下面的方式来返回 Translator 实例
        //return Container::get(Illuminate\Contracts\Translation\Translator::class);
    }
];
