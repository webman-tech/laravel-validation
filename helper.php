<?php

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use WebmanTech\LaravelValidation\Facades\Validator;

if (!function_exists('validator')) {
    /**
     * Create a new Validator instance.
     *
     * @return ($data is null ? \Illuminate\Contracts\Validation\Factory : \Illuminate\Contracts\Validation\Validator)
     */
    function validator(?array $data = null, array $rules = [], array $messages = [], array $attributes = []): ValidatorContract|ValidationFactory
    {
        $factory = Validator::instance();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data ?? [], $rules, $messages, $attributes);
    }
}
