<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_name',
        'date_time',
        'status'
    ];


    public function user()
    {
        return $this->belongsTo(UsersUser::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
