<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'template',
        'group',
        'claimer',
        'name',
        'description',
        'cost',
        'color',
        'icon',
        'claimed_date'
    ];
}
