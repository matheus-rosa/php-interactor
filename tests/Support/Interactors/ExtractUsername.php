<?php

namespace Tests\Support\Interactors;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class ExtractUsername
{
    use Interactable;

    protected function execute(Context $context)
    {
        $pieces = explode('@', $context->rawUsername);

        if (!empty($pieces)) {
            $context->username = $pieces[0];
        }
    }
}
