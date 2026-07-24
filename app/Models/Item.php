<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'price', 'status', 'image', 'category_id', 'created_by', 'updated_by'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
