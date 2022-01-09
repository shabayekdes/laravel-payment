# Laravel Payment Methods
[![Build Status](https://github.styleci.io/repos/421966331/shield?style=flat&branch=develop)](https://github.styleci.io/repos/421966331) [![Packagist version](https://img.shields.io/packagist/v/shabayek/laravel-payment)](https://packagist.org/packages/shabayek/laravel-payment) [![mit](https://img.shields.io/apm/l/laravel)](https://packagist.org/packages/shabayek/laravel-payment) ![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/shabayek/laravel-payment) ![Packagist Downloads](https://img.shields.io/packagist/dt/shabayek/laravel-payment)

This is a Laravel Package for Payment Gateway Integration. It has a clear and consistent API, is fully unit tested, and even comes with an example application to get you started.

> Note this package under development Don't use it in production.

### Usage

- Install laravel payment package with composer

```bash
composer require shabayek/laravel-payment
```

- Publish the config file with following command

```bash
php artisan vendor:publish --provider="Shabayek\Payment\PaymentServiceProvider" --tag=config
```

- Implement customer details contracts on user model

```php
use Shabayek\Payment\Contracts\CustomerContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements CustomerContract
{
    /**
     * Set address's details.
     *
     * @return array
     */
    public function customerDetails(): array
    {
        return [
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "phone" => $this->phone,
        ]
    }
}
```

- Implement address details contracts on address model

```php
use Shabayek\Payment\Contracts\AddressContract;
use Illuminate\Database\Eloquent\Model;

class Address extends Model implements AddressContract
{
    /**
     * Set address's details.
     *
     * @return array
     */
    public function addressDetails(): array
    {
        return [
            "apartment" => $this->apartment,
            "floor" => $this->floor,
            "city" => $this->city,
            "state" => $this->state,
            "street" => $this->street,
            "building" => $this->building,
        ]
    }
}
```

- Initiate a payment with the following code:

```php
$method_id = 1; // payment method id from the config file
$payment = Payment::store($method_id);
```

- You can insert user model object that implements **CustomerContract** or array 

```php
    $payment->customer($user);
    // OR array
    $payment->customer([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'test@test.com',
        'phone' => '09123456789',
    ]);
```

- You enter address model object that implements **AddressContract** array of data
```php
    $payment->address($address);
    // OR array
    $payment->address([
        "apartment" => "803",
        "floor" => "42",
        "street" => "Ethan Land",
        "building" => "8028",
        "postal_code" => "01898",
        "city" => "Jaskolskiburgh",
        "country" => "CR",
        "state" => "Utah"
    ]);
```

- Add items with loop array of data items
```php
    $items = [
        [
            "name" => "item1",
            "price" => 100,
            "quantity" => 2,
            "description" => "item1 description",
        ],
        [
            "name" => "item2",
            "price" => 200,
            "quantity" => 1,
            "description" => "item2 description",
        ],
    ];
    $payment->items($items);
    // OR By One
    $name = "item1";
    $price = 100;
    $quantity = 2; // Default 1
    $description = "item1 description"; // Default null

    $payment->addItem($name, $price, $quantity, $description);
```

- Check the payment is online to get pay url

```php
if ($payment->isOnline()) {
    $url = $payment->purchase();
}
```

- When callback from payment gateway, you can use the following code to verify the payment

```php
$payment = $payment->pay($request);

// function array with payment status and message
// return [
//     'success' => $isSuccess,
//     'message' => $message,
//     'data' => []
// ];
```

- Check the payment status
```php
$method_id = 1; // payment method id from the config file
$payment_order_id = 111; // payment order id
$payment_status = Payment::store($method_id)->verify($payment_order_id);
```
## License

The Laravel payment methods package is open-sourced software licensed under the [MIT license](https://github.com/shabayekdes/laravel-payment/blob/main/LICENSE).
