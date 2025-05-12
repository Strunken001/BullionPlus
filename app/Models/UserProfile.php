<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'id'            => 'integer',
        'user_id'       => 'integer',
        'country'       => 'string',
        'city'          => 'string',
        'state'         => 'string',
        'zip_code'      => 'string',
        'information'   => 'string',
        'reject_reason' => 'string',
    ];
}
