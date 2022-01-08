<?php

namespace Shabayek\Payment\Drivers;

use Shabayek\Payment\Contracts\AddressContract;
use Shabayek\Payment\Contracts\CustomerContract;

/**
 * Method abstract class.
 */
abstract class Method
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
    private $customer;
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
    private $items = [];
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
    }

    /**
     * Set credentials of payment methods.
     *
     * @return void
     */
    abstract protected function setCredentials($credentials);

    /**
     * Set the amount of transaction.
     *
     * @deprecated v0.5
     *
     * @param $amount
     * @return self
     *
     * @throws \Exception
     */
    public function amount($amount)
    {
        if (! is_numeric($amount)) {
            throw new \Exception('Amount value should be a number (integer or float).');
        }
        $this->amount = $amount;

        return $this;
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
     * @param  CustomerContract|array  $customer
     * @return self
     */
    public function customer($customer)
    {
        if (is_array($customer)) {
            $this->customer = $customer;
        }

        if ($customer instanceof CustomerContract) {
            $this->customer = $customer->customerDetails();
        }

        return $this;
    }

    /**
     * Get customer details.
     *
     * @param  string|null  $property
     * @return array|string
     */
    public function getCustomerDetails($property = null)
    {
        if ($this->customer == null) {
            throw new \InvalidArgumentException('Customer details not set.');
        }

        if ($property) {
            return $this->customer[$property] ?? 'NA';
        }

        return $this->customer;
    }

    /**
     * Get customer details.
     *
     * @param  string|null  $property
     * @return array|string
     */
    public function getAddressDetails($property = null)
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
     * Get items.
     *
     * @return array
     */
    public function getItems()
    {
        if (empty($this->items)) {
            throw new \Exception('Items not set.');
        }

        return $this->items;
    }

    /**
     * Set address details.
     *
     * @param  AddressContract|array  $address
     * @return self
     */
    public function address($address)
    {
        if (is_array($address)) {
            $this->address = $address;
        }

        if ($address instanceof AddressContract) {
            $this->address = $address->addressDetails();
        }

        return $this;
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
}
