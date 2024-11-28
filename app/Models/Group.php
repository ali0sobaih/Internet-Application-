<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'creating_date'
    ];

    public function file()
    {
        return $this->hasMany(File::class);
    }

    public function usergroup()
    {
        return $this->hasMany(UserGroup::class);
    }
}
    