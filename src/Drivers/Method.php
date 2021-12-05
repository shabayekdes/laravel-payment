<?php

namespace Shabayek\Payment\Drivers;

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
