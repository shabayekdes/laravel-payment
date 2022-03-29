<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Shabayek\Payment\Contracts\AddressContract;
use Shabayek\Payment\Contracts\CustomerContract;

/**
 * Method abstract class.
 */
abstract class AbstractMethod
{
    /**
     * Amount.
     *
     * @var int|float
     */
    protected $amount = 0;
    /**
     * Transaction id.
     *
     * @var int
     */
    protected $transaction_id;
    /**
     * Customer details.
     *
     * @var CustomerContract|array
     */
    protected $customer;
    /**
     * Address details.
     *
     * @var AddressContract|array
     */
    private $address;
    /**
     * Items details.
     *
     * @var array
     */
    protected $items = [];
    /**
     * errors.
     *
     * @var array
     */
    private $errors = [];

    /**
     * payment config.
     *
     * @var array
     */
    public $config = [];

    /**
     * Method constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->setCredentials($config['credentials']);
    }

    /**
     * Set credentials.
     *
     * @param  array  $credentials
     * @return void
     */
    protected function setCredentials(array $credentials)
    {
        $credentials = Arr::pluck($credentials, 'value', 'key');

        foreach ($credentials as $key => $value) {
            if (empty($value)) {
                $this->setErrors("Payment credentials ($key) are invalid.");
            }
            $this->$key = $value;
        }
    }

    /**
     * Set transaction id.
     *
     * @param $transaction
     * @return self
     */
    public function transaction($transaction)
    {
        $this->transaction_id = $transaction;

        return $this;
    }

    /**
     * Set customer details.
     *
     * @param  Model  $customer
     * @return self
     */
    public function customer($customer)
    {
        $this->customer = $customer;
        $this->address = $customer->billingDetails();

        return $this;
    }

    /**
     * Get customer details.
     *
     * @param  string|null  $property
     * @return array|string
     */
    protected function getCustomerDetails($property = null)
    {
        if ($this->customer == null) {
            throw new \InvalidArgumentException('Customer details not set.');
        }

        return $this->customer->customerDetails($property);
    }

    /**
     * Get customer details.
     *
     * @param  string|null  $property
     * @return array|string
     */
    protected function getBillingDetails($property = null)
    {
        if ($this->address == null) {
            throw new \InvalidArgumentException('Address details not set.');
        }

        if ($property) {
            return $this->address[$property] ?? 'NA';
        }

        return $this->address;
    }

    /**
     * Set items.
     *
     * @param  array  $item
     * @return self
     */
    public function items(array $item)
    {
        $this->items[] = $item;
        $this->amount += isset($item['amount_cents']) ? $item['amount_cents'] : 0;

        return $this;
    }

    /**
     * Add one item.
     *
     * @param  string  $name
     * @param  int  $price
     * @param  int  $quantity
     * @param  string  $description
     * @return self
     */
    public function addItem($name, $price, $quantity = 1, $description = null)
    {
        $this->items[] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'description' => $description,
        ];

        $this->amount += $price;

        return $this;
    }

    /**
     * Get is online boolean value.
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->config['is_online'];
    }

    /**
     * Get is active boolean value.
     *
     * @return bool
     */
    protected function isActive()
    {
        return $this->config['is_active'];
    }

    /**
     * Get is installment payment boolean value.
     *
     * @return bool
     */
    public function isInstallment()
    {
        return $this->config['is_installment'];
    }

    /**
     * Set errors.
     *
     * @param  string  $message
     * @return void
     */
    protected function setErrors($message)
    {
        $this->errors['success'] = false;
        $this->errors['message'][] = $message;
    }

    /**
     * Get error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors['message'] ?? [];
    }

    /**
     * Get success status.
     *
     * @return array
     */
    public function isSuccess()
    {
        return $this->errors['success'] ?? true;
    }
}
