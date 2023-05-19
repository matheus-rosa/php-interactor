<?php

namespace Tests\Unit;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\ContextFailureException;
use PHPUnit\Framework\TestCase;

final class ContextTest extends TestCase
{
    public function testInstantiatingContextWithParams()
    {
        $params = [
            'foo' => 'bar',
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
        ];

        $context = new Context($params);

        foreach ($params as $key => $value) {
            $this->assertSame($value, $context->{$key}, "The {$key} has a different value than expected: {$value}");
        }
    }

    public function testSuccess()
    {
        $context = new Context();
        $this->assertIsBool($context->success(), 'A bool value is expected');
    }

    public function testFailure()
    {
        $context = new Context();
        $this->assertIsBool($context->failure(), 'A bool value is expected');
    }

    public function testFailWithMessage()
    {
        $this->expectException(ContextFailureException::class);

        $context = new Context();
        $context->fail('something went wrong');
        $this->assertEquals(
            ['something went wrong'],
            $context->errors(),
            'The error array does not match the expected'
        );
        $this->assertFalse($context->strictMode, 'strictMode should be false');
    }

    public function testFailWithMessageAndStrictMode()
    {
        $this->expectException(ContextFailureException::class);

        $context = new Context();
        $context->fail('something went wrong', true);
        $this->assertEquals(
            ['something went wrong'],
            $context->errors(),
            'The error array does not match the expected'
        );
        $this->assertTrue($context->strictMode, 'strictMode should be true');
    }
}
