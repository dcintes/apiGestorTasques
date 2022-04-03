<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'email',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
