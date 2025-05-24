<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VTPassApi extends Model
{
    use HasFactory;

    protected $table = 'vtpass_apis';
    protected $guarded = ['id'];

    const PROVIDER_RELOADLY = 'RELOADLY';
    const GIFT_CARD = 'GIFT-CARD';
    const UTILITY_PAYMENT = 'UTILITY-PAYMENT';
    const MOBILE_TOPUP = 'MOBILE-TOPUP';
    const STATUS_ACTIVE = 1;
    const ENV_SANDBOX = 'SANDBOX';
    const ENV_PRODUCTION = 'PRODUCTION';

    protected $casts = [
        'type' => 'string',
        'provider' => 'string',
        'status' => 'integer',
        'env' => 'string',
        'credentials' => 'object',
    ];

    public function scopeGiftCard($query)
    {
        return $query->where('type', self::GIFT_CARD);
    }
    public function scopeUtilityPayment($query)
    {
        return $query->where('type', self::UTILITY_PAYMENT);
    }
    public function scopeMobileTopUp($query)
    {
        return $query->where('type', self::MOBILE_TOPUP);
    }
}
