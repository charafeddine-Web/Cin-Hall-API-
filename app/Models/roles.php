<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'permissions',
    ];
    protected $casts = [
        'permissions' => 'array',
    ];

    public function hasPermission($permission) {
        return in_array($permission , $this->permissions);
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
