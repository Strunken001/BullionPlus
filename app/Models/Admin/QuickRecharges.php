<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickRecharges extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    protected $casts = [
        'buttons' => 'object',
    ];
}
