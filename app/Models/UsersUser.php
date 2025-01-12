<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'first_name',
        'last_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usergroup()
    {
        return $this->hasMany(UserGroup::class);
    }
}
