<?php

namespace Shabayek\Payment\Tests\Unit;

use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class HandleErrorTest.
 *
 * @test
 */
class HandleErrorTest extends TestCase
{
    /** @test */
    public function test_error_when_forget_set_paymob_credentials()
    {
        $method_id = 2;
        $payment = Payment::store($method_id);
        $errors = $payment->getErrors();

        $this->assertIsArray($errors);
        $this->assertContains('Payment credentials (api_key) are invalid.', $errors);
    }
}
