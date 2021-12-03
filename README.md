# Laravel Payment Methods
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
