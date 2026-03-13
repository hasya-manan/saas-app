<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalLookup extends Model
{
    //
     protected $fillable = [
    'category', 
    'key', 
    'label', 
    'sort_order', 
    'is_active'
];
}
