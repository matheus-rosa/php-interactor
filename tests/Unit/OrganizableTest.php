<?php

namespace Tests\Unit;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Organizable;
use PHPUnit\Framework\TestCase;
use Tests\Support\Interactors\ExtractUsername;
use Tests\Support\Interactors\InvalidInteractor;
use Tests\Support\Interactors\TrimString;

final class OrganizableTest extends TestCase
{
    public function testOrganizeWithValidInteractors()
    {
        $class = new class() {
            use Organizable;

            protected function organize()
            {
                return [
                    TrimString::class,
                    ExtractUsername::class,
                ];
            }
        };

        $context = $class::call(['rawUsername' => ' matheus.rosa@somefancydomain123.com ']);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals('matheus.rosa', $context->username);
    }

    public function testOrganizeWithInvalidInteractor()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class '.InvalidInteractor::class.' must use the Interactable trait');

        $class = new class() {
            use Organizable;

            protected function organize()
            {
                return [
                    TrimString::class,
                    InvalidInteractor::class,
                ];
            }
        };

        $class::call(['rawUsername' => ' matheus.rosa@somefancydomain123.com ']);
    }
}
