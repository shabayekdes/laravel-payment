# Laravel Payment Methods
[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger) [![Packagist version](https://img.shields.io/packagist/v/shabayek/laravel-payment)](https://packagist.org/packages/shabayek/laravel-payment) [![mit](https://img.shields.io/apm/l/laravel)](https://packagist.org/packages/shabayek/laravel-payment) ![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/shabayek/laravel-payment) ![Packagist Downloads](https://img.shields.io/packagist/dt/shabayek/laravel-payment)

Laravel payment package handle all payment methods.

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

- Initiate a payment with the following code:

```php
$method_id = 1; // payment method id from the config file
$payment = Payment::store($method_id);
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
    $payment->items([
        "name" => "ASC1515",
        "amount_cents" => "500000",
        "description" => "Smart Watch",
        "quantity" => "1"
    ]);
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
