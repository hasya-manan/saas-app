<?php
namespace App\Models;

use App\Traits\BelongsToTenant; // Import the trait
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToTenant; // The magic happens here!

    protected $fillable = ['name', 'salary', 'tenant_id'];
}