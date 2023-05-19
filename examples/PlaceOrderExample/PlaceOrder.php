<?php

namespace Examples\PlaceOrderExample;

use MatheusRosa\PhpInteractor\Context;
use MatheusRosa\PhpInteractor\Organizable;
use PHPUnit\TextUI\XmlConfiguration\ValidationResult;

class PlaceOrder
{
    use Organizable;

    protected function around(Context $context)
    {
        $customerEmail = $context->customerEmail;
        $orderAttributes = $context->orderAttributes;

        if (!$this->isInputOrderValid($orderAttributes) || !$this->isCustomerEmailValid($customerEmail)) {
            // the pipeline defined in the `organize` method
            // WILL NOT run.
            echo 'the '. __CLASS__ .' Organizer will not run'.PHP_EOL;

            return false;
        }

        // the pipeline defined in the `organize` method
        // WILL run.
        echo 'the '.__CLASS__.' Organizer will run'.PHP_EOL;

        return true;
    }


    protected function organize()
    {
        return [
            CreateOrder::class,
            ChargeCard::class,
            SendThankYou::class,
        ];
    }

    private function isInputOrderValid($orderAttributes)
    {
        if (!\is_array($orderAttributes) || empty($orderAttributes)) {
            return false;
        }

        if (empty($orderAttributes['items']) || !\is_array($orderAttributes['items'])) {
            return false;
        }

        foreach ($orderAttributes['items'] as $item) {
            if (empty($item['code']) || empty($item['quantity'])) {
                return false;
            }

            // do other validations...
        }

        return true;
    }

    private function isCustomerEmailValid($customerEmail)
    {
        if (!\is_string($customerEmail) || trim($customerEmail) === '') {
            return false;
        }

        return true;
    }
}
