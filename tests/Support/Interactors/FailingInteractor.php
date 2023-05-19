<?php

namespace Tests\Support\Interactors;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class FailingInteractor
{
    use Interactable;

    protected function execute(Context $context)
    {
        $context->fail('error message');
    }
}
