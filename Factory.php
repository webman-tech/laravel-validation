<?php

namespace WebmanTech\LaravelValidation;

use Symfony\Component\Translation\Translator;

class Factory extends \Illuminate\Validation\Factory
{
    /**
     * @inheritDoc
     */
    public function make(array $data, array $rules, array $messages = [], array $attributes = [])
    {
        // 根据 symfony/translation 的 locale 自动切换当前 locale
        if (class_exists(Translator::class)) {
            $this->getTranslator()->setLocale(locale());
        }

        return parent::make($data, $rules, $messages, $attributes);
    }
}
