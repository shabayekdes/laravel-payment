<?php

namespace Shabayek\Payment\Tests\Fixtures;

use Shabayek\Payment\Concerns\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    use Billable, Notifiable;

    /**
     * Get the address associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class)->withDefault();
    }
}
