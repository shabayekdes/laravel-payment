<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaymobOrderTest.
 *
 * @test
 */
class PaymobOrderTest extends TestCase
{
    /** @test*/
    public function test_create_order_without_set_customer_details()
    {
        $this->expectException(\Exception::class);

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 200),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $token = Str::random(512);
        $order = $this->callMethod($payment, 'orderCreation', [$token]);
    }

    /** @test*/
    public function test_create_order_without_items_details()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Items not set.');

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 200),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());

        $token = Str::random(512);
        $this->callMethod($payment, 'orderCreation', [$token]);
    }

    /** @test*/
    public function test_create_order_without_customer_details()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer details not set.');

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
        ]);

        // Fake Data
        $item = $this->items();

        $method_id = 2;
        $payment = Payment::via($method_id);
        $payment->addItem($item['name'], $item['price'], $item['quantity'], $item['description']);

        $payment->purchase();
    }

    /** @test*/
    public function test_create_order_success()
    {
        $order_id = rand(1, 100);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response(['id' => $order_id], 200),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());
        $payment->items($this->items());

        $token = Str::random(512);
        $order = $this->callMethod($payment, 'orderCreation', [$token]);

        $this->assertTrue(isset($order['id']));
        $this->assertEquals($order_id, $order['id']);
    }

    /** @test */
    public function it_can_add_item_by_one_with_paymob_success()
    {
        // Fake Data
        $item = $this->items();
        $payment = Payment::via(2);

        $payment->addItem($item['name'], $item['price'], $item['quantity'], $item['description']);
        $items = $this->callMethod($payment, 'getItems');

        $this->assertEquals($items[0]['name'], $item['name']);
        $this->assertEquals($items[0]['amount_cents'], $item['price'] * 100);
        $this->assertEquals($items[0]['quantity'], $item['quantity']);
        $this->assertEquals($items[0]['description'], $item['description']);
    }

    /** @test */
    public function it_can_add_item_by_one_with_paymob_success_with_default_details()
    {
        // Fake Data
        $item = $this->items();
        $payment = Payment::via(2);

        $payment->addItem($item['name'], $item['price']);
        $items = $this->callMethod($payment, 'getItems');

        $this->assertEquals($items[0]['quantity'], 1);
        $this->assertEquals($items[0]['description'], 'NA');
    }

    /**
     * Get items fake data.
     *
     * @return array
     */
    private function items(): array
    {
        return [
            'name'         => 'Product name',
            'description'  => 'Product description',
            'price' => 150,
            'quantity'     => 1,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('payment.stores.2.credentials', [
            'api_key'   => 'test',
            'hmac_hash'   => 'test',
            'merchant_id'   => 'test',
            'iframe_id'   => 'test',
            'integration_id'   => 'test',
        ]);
    }
}
