<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\StaffFinance;
use App\Traits\HasCustomPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // 1. REMOVE::The HasUuids trait is designed to turn the id into a UUID. 
    // If you use it, it will break your auto-incrementing id.
    use HasFactory, Notifiable, BelongsToTenant, SoftDeletes, HasCustomPagination;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role_id',
        'department_id',
        'supervisor_id',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * This completely hides them from browser console logs, dd(), logger(),
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'tenant_id', 
        'role_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

        /**
     * Add this method! 
     * Even with BelongsToTenant trait, you need this to access $user->tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function finance()
    {
        return $this->hasOne(StaffFinance::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function hasRole(string $role): bool
    {
        // Adjust this logic to match how you store roles (e.g., a 'role' column)
        return $this->role === $role;
    }
    //filtering 
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tenant_id', 'like', "%{$search}%");
            });
        })->when($filters['role'] ?? null, function ($q, $role) {
            $q->whereHas('role', function ($inner) use ($role) {
                $inner->where('name', $role);
            });
        })->when($filters['tenant_id'] ?? null, function ($q, $tenantId) {
            $q->where('tenant_id', $tenantId);
        });
        $query->when($filters['department_id'] ?? null, function ($q, $departmentId) {
        // Assuming your User model has a 'department_id' column
        $q->where('department_id', $departmentId);
        
        
    });
    }

   // 2. Add this to automatically create the UUID when a user is born
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // 3. Keep this: "When you see a User in the URL, look at the uuid column.instead display a raw id"
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
