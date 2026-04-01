<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class UserProfile extends Model
{
    use BelongsToTenant;
    //
    protected $fillable = [
        'user_id',
        'tenant_id',
        'phone_number',
        'ic_number',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'postcode',
        'state',
        'emergency_contact_name',
        'emergency_contact_phone',
        'join_date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
