<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'user',
        'admin',
    ];

    public function userm()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function groupm()
    {
        return $this->belongsTo(Group::class, 'post');
    }
}
