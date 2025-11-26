<?php

namespace App\Services;

class PaymentService
{
    public function processPayment()
    {
        return rand(0, 100) < 50;
    }
}
