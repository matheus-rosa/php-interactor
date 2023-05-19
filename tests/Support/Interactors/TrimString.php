<?php

namespace Tests\Support\Interactors;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class TrimString
{
    use Interactable;

    public function rollback(Context $context)
    {
        $context->rawUsername = $context->originalRawUsername;
    }

    protected function execute(Context $context)
    {
        $context->originalRawUsername = $context->rawUsername;
        $context->rawUsername = trim($context->rawUsername);
    }
}
