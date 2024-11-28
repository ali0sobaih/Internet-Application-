<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_id',
        'path',
        'status',
        'author'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function archive()
    {
        return $this->hasMany(Archive::class);
    }
}
