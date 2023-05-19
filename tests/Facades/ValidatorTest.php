<?php

namespace WebmanTech\LaravelValidation\Tests\Facades;

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;
use Throwable;
use WebmanTech\LaravelValidation\Facades\Validator;

/**
 * https://laravel.com/docs/10.x/validation
 */
class ValidatorTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(FactoryContract::class, Validator::instance());
        $this->assertInstanceOf(FactoryContract::class, validator());
        $this->assertInstanceOf(ValidatorContract::class, validator(['title' => '123']));
    }

    public function testValidate()
    {
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
        $this->assertEquals($data1, $data);
        // validate error
        try {
            Validator::validate($data1, $errorRules);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ValidationException::class, $e);
        }
        // manual validate
        // ok
        $validator = Validator::make($data, $okRules);
        $this->assertFalse($validator->fails());
        $this->assertEquals($data, $validator->validated());
        // fails
        $validator = Validator::make($data, $errorRules);
        $this->assertTrue($validator->fails());
        $this->assertEquals(1, $validator->errors()->count());
    }

    public function testMessages()
    {
        try {
            Validator::validate([
                'title' => ''
            ], [
                'title' => 'required'
            ], [
                'title.required' => '标题不能为空'
            ]);
        } catch (ValidationException $e) {
            $this->assertEquals('标题不能为空', $e->errors()['title'][0]);
        }
    }

    public function testRules()
    {
        $this->assertTrue(true);
        // https://laravel.com/docs/10.x/validation#available-validation-rules
    }
}
