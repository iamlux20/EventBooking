<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_process_payment_returns_boolean()
    {
        $service = new PaymentService();
        $result = $service->processPayment();

        $this->assertIsBool($result);
    }

    public function test_process_payment_can_return_true()
    {
        $service = new PaymentService();

        // Run multiple times to increase chance of getting true
        $results = [];
        for ($i = 0; $i < 100; $i++) {
            $results[] = $service->processPayment();
        }

        $this->assertContains(true, $results);
    }

    public function test_process_payment_can_return_false()
    {
        $service = new PaymentService();

        // Run multiple times to increase chance of getting false
        $results = [];
        for ($i = 0; $i < 100; $i++) {
            $results[] = $service->processPayment();
        }

        $this->assertContains(false, $results);
    }
}
