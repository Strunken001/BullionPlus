<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Constants\GlobalConst;
use App\Models\Frontend\BlogsCategory;
use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'object',
    ];

    public function getRouteKeyName()
    {
        return "slug";
    }

    public function category()
    {
        return $this->belongsTo(BlogsCategory::class, "category_id");
    }

    public function scopeActive($query)
    {
        return $query->where("status", GlobalConst::ACTIVE);
    }
}
