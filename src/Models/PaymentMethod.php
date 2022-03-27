<?php

namespace Shabayek\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the credentials for the PaymentMethod.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credentials()
    {
        return $this->hasMany(PaymentCredential::class);
    }
}
