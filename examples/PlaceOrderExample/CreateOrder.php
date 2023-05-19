<?php

namespace Examples\PlaceOrderExample;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Interactable;

class CreateOrder
{
    use Interactable;

    protected function execute(Context $context)
    {
        $context->order = new Order($context->orderAttributes['items'], $context->customerEmail);

        if (!$context->order->save()) {
            $context->fail($context->order->fullErrorMessages());
        }
    }
}
