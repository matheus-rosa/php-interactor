<?php

namespace Tests\Support\Interactors;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class ShuffleUsername
{
    use Interactable;

    protected function execute(Context $context)
    {
        $username = str_split($context->username);

        shuffle($username);

        $context->username = implode('', $username);
    }
}
