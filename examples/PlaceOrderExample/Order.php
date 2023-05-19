<?php

namespace Examples\PlaceOrderExample;

class Order
{
    public $id;

    public $items;

    public $customerEmail;

    public function __construct($items, $customerEmail)
    {
        $this->items = $items;
        $this->customerEmail = $customerEmail;
    }

    public function save()
    {
        $this->id = uniqid();

        return true;
    }

    public function fullErrorMessages()
    {
        return ['error messages'];
    }
}
