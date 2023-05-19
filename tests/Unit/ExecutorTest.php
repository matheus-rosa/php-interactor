<?php

namespace Tests\Unit;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\ContextFailureException;
use MatheusRosa\PhpInteractor\Executor;
use PHPUnit\Framework\TestCase;

final class ExecutorTest extends TestCase
{
    public function testCallWithContextAsArgument()
    {
        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
            }
        };

        $inputContext = new Context();
        $outputContext = $class::call($inputContext);

        $this->assertInstanceOf(Context::class, $outputContext);
        $this->assertEquals($inputContext, $outputContext);
        $this->assertTrue($outputContext->success());
        $this->assertFalse($outputContext->failure());
    }

    public function testCallWithArrayAsArgument()
    {
        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
            }
        };

        $context = $class::call(['foo' => 'bar']);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals('bar', $context->foo);
        $this->assertTrue($context->success());
        $this->assertFalse($context->failure());
    }

    public function testCallWithUnexpectedArgument()
    {
        $this->expectException(\TypeError::class);

        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
            }
        };

        $class::call(null);
    }

    public function testCallWithoutParamsAndCreatingContextVariablesOnTheFly()
    {
        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
                $context->currentDate = new \DateTime();
            }
        };

        $context = $class::call();

        $this->assertInstanceOf(Context::class, $context);
        $this->assertTrue($context->success());
        $this->assertFalse($context->failure());
        $this->assertInstanceOf(\DateTime::class, $context->currentDate);
    }

    public function testCallWithParamsAndModifyingThem()
    {
        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
                $context->even *= 2;
                $context->odd += 2;
            }
        };

        $context = $class::call([
            'even' => 2,
            'odd' => 3,
        ]);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertTrue($context->success());
        $this->assertFalse($context->failure());
        $this->assertEquals(4, $context->even);
        $this->assertEquals(5, $context->odd);
    }

    public function testFailingContextWithoutStrictMode()
    {
        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
                $context->fail('error message');
            }
        };

        $context = $class::call();

        $this->assertFalse($context->success());
        $this->assertTrue($context->failure());
        $this->assertEquals(['error message'], $context->errors());
    }

    public function testFailingContextWithStrictMode()
    {
        $this->expectException(ContextFailureException::class);

        $class = new class() {
            use Executor;

            protected function perform(Context $context)
            {
                $context->fail('error message', true);
            }
        };

        $context = $class::call();

        $this->assertFalse($context->success());
        $this->assertTrue($context->failure());
        $this->assertEquals(['error message'], $context->errors());
    }

    public function testSkipExecutionOnAroundMethod()
    {
        $class = new class() {
            use Executor;

            protected function around(Context $context)
            {
                return $context->number !== 2;
            }

            protected function perform(Context $context)
            {
                $context->number *= 2;
            }
        };

        $context = $class::call(['number' => 2]);

        $this->assertEquals(2, $context->number);
    }

    public function testDoNotSkipInAroundMethod()
    {
        $class = new class() {
            use Executor;

            protected function around(Context $context)
            {
                return $context->number === 2;
            }

            protected function perform(Context $context)
            {
                $context->number *= 2;
            }
        };

        $context = $class::call(['number' => 2]);

        $this->assertEquals(4, $context->number);
    }

    public function testBeforeMethod()
    {
        $class = new class() {
            use Executor;

            protected function before(Context $context)
            {
                $context->number += 1;
            }

            protected function perform(Context $context)
            {
                $context->number += 1;
            }
        };

        $context = $class::call();

        $this->assertEquals(2, $context->number);
    }

    public function testBeforeMethodWithAround()
    {
        $class = new class() {
            use Executor;

            protected function around(Context $context)
            {
                $context->number = 1;
            }

            protected function before(Context $context)
            {
                $context->number += 1;
            }

            protected function perform(Context $context)
            {
                $context->number += 1;
            }
        };

        $context = $class::call();

        $this->assertEquals(3, $context->number);
    }

    public function testAfterMethod()
    {
        $class = new class() {
            use Executor;

            protected function after(Context $context)
            {
                $context->cleanDatabase = false;
            }

            protected function perform(Context $context)
            {
            }
        };

        $context = $class::call(['cleanDatabase' => true]);

        $this->assertFalse($context->cleanDatabase);
    }

    public function testAfterMethodWithAround()
    {
        $class = new class() {
            use Executor;

            protected function around(Context $context)
            {
                $context->number += 1;
            }

            protected function after(Context $context)
            {
                $context->number += 1;
            }

            protected function perform(Context $context)
            {
            }
        };

        $context = $class::call(['number' => 1]);

        $this->assertEquals(3, $context->number);
    }

    public function testAfterMethodWithAroundAndBefore()
    {
        $class = new class() {
            use Executor;

            protected function around(Context $context)
            {
                $context->number += 1;
            }

            protected function before(Context $context)
            {
                $context->number += 1;
            }

            protected function after(Context $context)
            {
                $context->number += 1;
            }

            protected function perform(Context $context)
            {
            }
        };

        $context = $class::call(['number' => 1]);

        $this->assertEquals(4, $context->number);
    }
}
