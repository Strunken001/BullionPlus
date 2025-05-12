<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Frontend\Blogs;
use Illuminate\Database\Eloquent\Model;

class BlogsCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'name'    => "object",
    ];

    public function blogs() {
        return $this->hasMany(Blogs::class);
    }
}
