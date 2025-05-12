<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSections extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'        => 'integer',
        'key'       => 'string',
        'value'     => 'object',
        'status'    => 'integer',
        'serialize' => 'integer',
    ];

    public function scopeSiteCookie() {
       return $this->where('key','site_cookie')->first();
    }

    public function scopeGetData($query,$slug) {
        return $this->where("key",$slug);
    }

}
