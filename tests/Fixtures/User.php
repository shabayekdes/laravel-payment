<?php

namespace Shabayek\Payment\Tests\Fixtures;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Notifications\Notifiable;
use Shabayek\Payment\Concerns\Billable;

class User extends Model
{
    use Billable, Notifiable;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];
    /**
     * Get the address associated with the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class)->withDefault();
    }
}
