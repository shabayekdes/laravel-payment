<?php

namespace Shabayek\Payment\Tests\Unit;

use Mockery\MockInterface;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\Fixtures\Shipping;
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

        $payment = Payment::via(2);
        $payment->customer($mock);

        $customerDetails = $this->callMethod($payment, 'getCustomerDetails');

        $this->assertEquals('John', $customerDetails['first_name']);
        $this->assertEquals('Doe', $customerDetails['last_name']);
    }

    /** @test*/
    public function test_can_get_customer_details_first_name_from_name_column_successfully()
    {
        // Mock user model
        $mock = $this->partialMock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAttribute')->once()->with('name')->andReturn('name');
            $mock->shouldReceive('firstNameColumn')->once()->andReturn($mock->name);
        });
        $mock->first_name = 'Changed';
        $mock->last_name = 'Doe';
        $mock->email = 'test@test.com';
        $mock->phone = '01000000000';

        $payment = Payment::via(2);
        $payment->customer($mock);

        $customerDetails = $this->callMethod($payment, 'getCustomerDetails');

        $this->assertEquals('name', $customerDetails['first_name']);
    }

    /** @test*/
    public function test_can_get_customer_details_with_changing_address_relation_successfully()
    {
        // Mock user model
        $address = [
            'apartment' => 'Test apartment',
            'floor'     => '1',
            'city'      => 'Test city',
            'state'     => 'Test state',
            'street'    => 'Test street',
            'building'  => 'Test building',
        ];
        $mock = $this->partialMock(User::class, function (MockInterface $mock) use ($address) {
            $mock->shouldReceive('get')->once()->andReturn(new Shipping($address));
            $mock->shouldReceive('billingRelation')->andReturn($mock->get());
        });

        $payment = Payment::via(2);
        $payment->customer($mock);

        $billingDetails = $this->callMethod($payment, 'getBillingDetails');

        $this->assertSame($address, $billingDetails);
    }
}
