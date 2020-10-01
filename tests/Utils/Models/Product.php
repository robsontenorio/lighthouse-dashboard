<?php

namespace Tests\Utils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function person(): BelongsTo
    {
        // Force to test internal error. "Person" model does not exists.
        return $this->belongsTo(Person::class);
    }

    public function badges(): HasMany
    {
        // Force to test internal error. It should be "hasMany" relationship.
        return 'Ops!';
    }
}
