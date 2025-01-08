<?php

use WebmanTech\LaravelValidation\Translation\WebmanSymfonyTranslator;

beforeEach(function () {
    $this->translator = new WebmanSymfonyTranslator('en');
    locale('en');
});

test('locale', function () {
    expect($this->translator->getLocale())->toBe('en');

    // 全局修改的不会实时修改 translator 的 locale
    locale('zh_CN');
    expect($this->translator->getLocale())->toBe('en');

    // 通过 translator->setLocale 设置的 locale 不会影响到全局 locale
    $this->translator->setLocale('zh_TW');
    expect($this->translator->getLocale())->toBe('zh_TW')
        ->and(locale())->toBe('zh_CN');
});

test('get', function () {
    $validationEn = require base_path('resource/translations/en/validation.php');
    $validationZhCN = require base_path('resource/translations/zh_CN/validation.php');

    // 指定 locale
    expect($this->translator->get('validation.required'))->toBe($validationEn['required'])
        ->and($this->translator->get('validation.required', [], 'zh_CN'))->toBe($validationZhCN['required']);

    // replace
    $replace = ['attribute' => 'title'];
    $strReplace = [':attribute' => 'title'];
    expect($this->translator->get('validation.required', $replace, 'zh_CN'))->toBe(strtr($validationZhCN['required'], $strReplace));

    // 嵌套
    $replace = ['attribute' => 'title', 'min' => 1, 'max' => 100];
    $strReplace = [':attribute' => 'title', ':min' => 1, ':max' => 100];
    expect($this->translator->get('validation.between.numeric', $replace, 'zh_CN'))->toBe(strtr($validationZhCN['between']['numeric'], $strReplace));
});

test('choice', function () {
    expect($this->translator->choice('validation-for-symfony.custom.apples', 0))->toBe('none')
        ->and($this->translator->choice('validation-for-symfony.custom.apples', 1))->toBe('have some')
        ->and($this->translator->choice('validation-for-symfony.custom.apples', 20))->toBe('have much')
        ->and($this->translator->choice('validation-for-symfony.custom.apples', 0, [], 'zh_CN'))->toBe('没有')
        ->and($this->translator->choice('validation-for-symfony.custom.apples', 1, [], 'zh_CN'))->toBe('有一些')
        ->and($this->translator->choice('validation-for-symfony.custom.apples', 20, [], 'zh_CN'))->toBe('有很多');
});
