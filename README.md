# Laravel Payment Methods
[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger) [![Packagist version](https://img.shields.io/packagist/v/shabayek/laravel-payment)](https://packagist.org/packages/shabayek/laravel-payment) [![mit](https://img.shields.io/apm/l/laravel)](https://packagist.org/packages/shabayek/laravel-payment)

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
