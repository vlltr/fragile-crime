<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'category_id');
    }
}
