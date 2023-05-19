<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Examples\PlaceOrderExample\PlaceOrder;

try {
    $context = PlaceOrder::call([
        'orderAttributes' => [
            'items' => [
                [
                    'code' => 'abc',
                    'quantity' => 3,
                ],
            ],
        ],
        'customerEmail' => 'john.doe@email.com',
    ]);

    var_dump('$context->success()', $context->success());
    var_dump('order', $context->order);
} catch (Exception $e) {
    var_dump('$e->getMessage()', $e->getMessage());
}
