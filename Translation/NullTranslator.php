<?php

namespace WebmanTech\LaravelValidation\Translation;

use Illuminate\Contracts\Translation\Translator;

class NullTranslator implements Translator
{
    /**
     * @inheritDoc
     */
    public function get($key, array $replace = [], $locale = null)
    {
        return strtr($key, $replace);
    }

    /**
     * @inheritDoc
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return strtr($key, $replace);
    }

    /**
     * @inheritDoc
     */
    public function getLocale()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setLocale($locale)
    {
    }
}