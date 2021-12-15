<?php

namespace Shabayek\Payment\Drivers;

use Shabayek\Payment\Contracts\AddressContract;
use Shabayek\Payment\Contracts\CustomerContract;

/**
 * Method abstract class
 * @package Shabayek\Payment\Drivers
 */
abstract class Method
{
    /**
     * Amount
     *
     * @var int|float
     */
    protected $amount = 0;
    /**
     * Transaction id
     *
     * @var int
     */
    protected $transaction_id;
    /**
     * Customer details
     *
     * @var CustomerContract
     */
    protected $customer;
    /**
     * Address details
     *
     * @var AddressContract
     */
    protected $address;
    /**
     * Items details
     *
     * @var array
     */
    public $items = [];
    /**
     * payment config
     *
     * @var array
     */
    public $config = [];
    /**
     * Method constructor.
     *
     * @param Array $config
     */
    public function __construct(Array $config)
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
     * @param $amount
     * @return $this
     * @throws \Exception
     */
    public function amount($amount)
    {
        if (!is_numeric($amount)) {
            throw new \Exception('Amount value should be a number (integer or float).');
        }
        $this->amount = $amount;

        return $this;
    }
    /**
     * Set transaction id.
     *
     * @param $transaction
     * @return $this
     */
    public function transaction($transaction)
    {
        $this->transaction_id = $transaction;

        return $this;
    }
    /**
     * Set customer details.
     *
     * @param CustomerContract|array $customer
     * @return void
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
     * @param string|null $property
     * @return void
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
     * @param AddressContract $address
     * @return void
     */
    public function address(AddressContract $address)
    {
        $this->address = $address->addressDetails();

        return $this;
    }
    /**
     * Set items
     *
     * @param array $item
     * @return void
     */
    public function items(array $item)
    {
        $this->items[] = $item;

        return $this;
    }
    /**
     * @return mixed
     */
    public function isOnline()
    {
        return $this->config['is_online'];
    }
    /**
     * @return mixed
     */
    protected function isActive()
    {
        return $this->config['is_active'];
    }
    /**
     * @return mixed
     */
    public function isInstallment()
    {
        return $this->config['is_installment'];
    }
}
