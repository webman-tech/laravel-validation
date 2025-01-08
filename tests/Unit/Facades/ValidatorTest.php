<?php

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use WebmanTech\LaravelValidation\Facades\Validator;


test('instance', function () {
    expect(Validator::instance())->toBeInstanceOf(FactoryContract::class);
    expect(validator())->toBeInstanceOf(FactoryContract::class);
    expect(validator(['title' => '123']))->toBeInstanceOf(ValidatorContract::class);
});

test('validate', function () {
    $data1 = [
        'email' => '123',
    ];
    $okRules = [
        'email' => 'required|string',
    ];
    $errorRules = [
        'email' => 'required|email',
    ];

    // validate ok
    $data = Validator::validate($data1, $okRules);
    expect($data)->toEqual($data1);

    // validate error
    try {
        Validator::validate($data1, $errorRules);
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(ValidationException::class);
    }

    // manual validate
    // ok
    $validator = Validator::make($data, $okRules);
    expect($validator->fails())->toBeFalse();
    expect($validator->validated())->toEqual($data);

    // fails
    $validator = Validator::make($data, $errorRules);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->count())->toEqual(1);
});

test('messages', function () {
    // 自定义消息
    try {
        Validator::validate([
            'title' => ''
        ], [
            'title' => 'required'
        ], [
            'title.required' => '标题不能为空'
        ]);
    } catch (ValidationException $e) {
        expect($e->errors()['title'][0])->toEqual('标题不能为空');
    }
});

test('rules', function () {
    expect(true)->toBeTrue();
    // https://laravel.com/docs/11.x/validation#available-validation-rules
});
