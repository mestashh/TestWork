<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(array $array)
 */
class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function products(): HasMany
    {
        return $this->HasMany(Product::class);
    }
}
