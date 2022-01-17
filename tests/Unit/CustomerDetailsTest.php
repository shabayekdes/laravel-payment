<?php

namespace Shabayek\Payment\Tests\Unit;

use Mockery\MockInterface;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\Fixtures\User;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class CustomerDetailsTest.
 *
 * @test
 */
class CustomerDetailsTest extends TestCase
{
    /** @test*/
    public function test_can_get_customer_details_successfully()
    {
        // Mock user model
        $mock = $this->partialMock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('firstNameColumn')->once()->andReturn('John');
        });
        $mock->first_name = 'Changed';
        $mock->last_name = 'Doe';
        $mock->email = 'test@test.com';
        $mock->phone = '01000000000';

        $payment = Payment::store(2);
        $payment->customer($mock);

        $customerDetails = $this->callMethod($payment, 'getCustomerDetails');

        $this->assertEquals('John', $customerDetails['first_name']);
        $this->assertEquals('Doe', $customerDetails['last_name']);
    }
}
