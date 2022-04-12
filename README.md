# Laravel Payment Methods

[![Github Status](https://github.com/shabayekdes/laravel-payment/actions/workflows/tests.yml/badge.svg)](https://github.com/shabayekdes/laravel-payment/actions) [![Styleci Status](https://github.styleci.io/repos/421966331/shield?style=flat&branch=develop)](https://github.styleci.io/repos/421966331) [![Packagist version](https://img.shields.io/packagist/v/shabayek/laravel-payment)](https://packagist.org/packages/shabayek/laravel-payment) [![mit](https://img.shields.io/apm/l/laravel)](https://packagist.org/packages/shabayek/laravel-payment) ![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/shabayek/laravel-payment) ![Packagist Downloads](https://img.shields.io/packagist/dt/shabayek/laravel-payment)

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

- Initiate a payment with the following code:

```php
$method_id = 1; // payment method id from the config file
$payment = Payment::via($method_id);
```

- Implement customer details contracts on user model by adding **Billable** trait

```php
use Shabayek\Payment\Concerns\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Billable, Notifiable;

}
```

- Following columns is default for billable user
  - **first_name**
  - **last_name**
  - **email**
  - **phone**

if you want to change the column name you can do add public methods on your model with convention name CamelCase like **firstName** + **Column**
FirstNameColumn

```php
    /**
     * Get the first name.
     *
     * @return string|null
     */
    public function firstNameColumn()
    {
        return $this->name; // OR $this->first_name;
    }
```

- Implement address details with relation to address model
  > The default relation is **address** if you want change the relation name you can do add public methods on your user model

```php
    /**
     * Get the billable billing relations.
     *
     * @return Model
     */
    public function billingRelation()
    {
        return $this->address_relation; // OR $this->address;
    }
```

- Pass the user model to payment gateway

```php
$payment->customer($user);
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

- Set transaction id will send to gateway

```php
$payment->transaction($transaction_id);
```

- Check the payment is online to get pay url

```php
if ($payment->isOnline()) {
    $url = $payment->purchase();
}
```

- The MasterCard method adds a new way to get the checkout form
> pass the transaction model will update successIndicator and return view with checkout form

```php
$payment->checkoutForm(Transaction $transaction);
```

- Print the errors messages

```php
$payment->getErrors();
$payment->isSuccess();
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
$payment_status = Payment::via($method_id)->verify($payment_order_id);
```

## Change log

Please see [CHANGELOG](https://github.com/shabayekdes/laravel-payment/blob/main/CHANGELOG.md) for more information on what has been changed recently.


## Contributing

Please see [CONTRIBUTING](https://github.com/shabayekdes/laravel-payment/blob/main/CONTRIBUTING.md) for details.


## Security Vulnerabilities

If you've found a bug regarding security please mail [esmail.shabayek@gmail.com](mailto:esmail.shabayek@gmail.com) instead of using the issue tracker.


## License

The Laravel payment methods package is open-sourced software licensed under the [MIT license](https://github.com/shabayekdes/laravel-payment/blob/main/LICENSE).
