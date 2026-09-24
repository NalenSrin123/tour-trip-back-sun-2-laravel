<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissions()
    {
        // Links to the 'role_permissions' pivot table shown in your ERD
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        // Links to the 'user_roles' pivot table shown in your ERD
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
