<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffFinance extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'basic_salary',
        'bank_name',
        'bank_account_no',
        'epf_no',
        'epf_rate_employee',
        'epf_rate_employer',
        'socso_no',
        'socso_type',
        'tax_no',
        'eis_enabled',
    ];
}
