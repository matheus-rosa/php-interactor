<?php

namespace Tests\Unit;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;
use PHPUnit\Framework\TestCase;

final class InteractableTest extends TestCase
{
    public function testExecute()
    {
        $class = new class {
            use Interactable;

            protected function execute(Context $context)
            {
                $context->number *= 2;
            }
        };

        $context = $class::call(['number' => 2]);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals(4, $context->number);
    }
}
