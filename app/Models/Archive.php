<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'version',
        'date',
        'operation',
    ];


    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function editor()
    {
        return $this->hasOne(Editor::class);
    }
}
