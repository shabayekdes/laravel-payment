<?php

namespace Shabayek\Payment\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Billable
{
    /**
     * Get the first name.
     *
     * @return string|null
     */
    public function firstNameColumn()
    {
        return $this->first_name ?? $this->name;
    }

    /**
     * Get the last name.
     *
     * @return string|null
     */
    public function lastNameColumn()
    {
        return $this->last_name ?? $this->name;
    }

    /**
     * Get the email billing.
     *
     * @return string|null
     */
    public function emailColumn()
    {
        return $this->email;
    }

    /**
     * Get the phone number.
     *
     * @return string|null
     */
    public function phoneColumn()
    {
        return $this->phone;
    }

    /**
     * Get the billable billing relations.
     *
     * @return Model
     */
    public function billingRelation()
    {
        return $this->address;
    }

    /**
     * Set customer's details.
     *
     * @return array
     */
    public function customerDetails($property): array
    {
        if ($property) {
            $column = Str::camel($property).'Column';

            return $this->$column() ?? 'NA';
        }

        return [
            'first_name' => $this->firstNameColumn(),
            'last_name' => $this->lastNameColumn(),
            'email' => $this->emailColumn(),
            'phone' => $this->phoneColumn(),
        ];
    }

    /**
     * Set billing's details.
     *
     * @return array
     */
    public function billingDetails(): array
    {
        return [
            'apartment' => $this->billingRelation()->apartment,
            'floor' => $this->billingRelation()->floor,
            'city' => $this->billingRelation()->city,
            'state' => $this->billingRelation()->state,
            'street' => $this->billingRelation()->street,
            'building' => $this->billingRelation()->building,
        ];
    }
}
