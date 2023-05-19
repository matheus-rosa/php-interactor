<?php

namespace Examples\PlaceOrderExample;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class SendThankYou
{
    use Interactable;

    protected function execute(Context $context)
    {
        echo "dispatching an email to the client confirming the purchase...\n";
    }
}
