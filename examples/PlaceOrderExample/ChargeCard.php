<?php

namespace Examples\PlaceOrderExample;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class ChargeCard
{
    use Interactable;

    protected function execute(Context $context)
    {
        // some integration will be handled here
        echo "charging an amount in the customer's card...\n";
    }
}
