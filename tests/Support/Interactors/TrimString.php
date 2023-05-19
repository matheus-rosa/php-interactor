<?php

namespace Tests\Support\Interactors;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class TrimString
{
    use Interactable;

    protected function execute(Context $context)
    {
        $context->rawUsername = trim($context->rawUsername);
    }
}
