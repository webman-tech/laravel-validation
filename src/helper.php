<?php

use WebmanTech\LaravelValidation\Facedes\Validator;

if (!function_exists('validator')) {
    /**
     * Laravel 验证器
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $factory = Validator::instance();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}
