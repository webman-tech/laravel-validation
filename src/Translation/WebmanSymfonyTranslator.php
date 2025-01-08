<?php

namespace WebmanTech\LaravelValidation\Translation;

use Illuminate\Contracts\Translation\Translator;

class WebmanSymfonyTranslator implements Translator
{
    protected string $locale;

    public function __construct(?string $locale = null)
    {
        $this->setLocale($locale ?: locale());
    }

    /**
     * @inheritDoc
     */
    public function get($key, array $replace = [], $locale = null)
    {
        $locale = $locale ?: $this->locale;

        $arr = explode('.', $key);
        $domain = $arr[0];
        unset($arr[0]);
        $id = implode('.', $arr);

        $newReplace = [];
        foreach ($replace as $key => $value) {
            if (!in_array($key[0], ['%', ':'])) {
                $key = ':' . $key;
            }
            $newReplace[$key] = $value;
        }

        return trans($id, $newReplace, $domain, $locale);
    }

    /**
     * @inheritDoc
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
        $replace['%count%'] = $number;
        // 注意：symfony 下使用 Inf 和 -Inf 表示区间的最大值和最小值，但 laravel 下使用 * 的，此处暂时未处理
        return $this->get($key, $replace, $locale);
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }
}
