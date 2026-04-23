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
        'phone_number',
        'ic_number',
        'user_gender',
         'dob',
        'phone',
        'join_date',
        'gender',
        'address_line_1',
        'address_line_2',
        'city',
        'postcode',
        'state',
        'waris_name',
        'waris_gender',
        'waris_relationship',
        'waris_ic ',
        'waris_phone'

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
