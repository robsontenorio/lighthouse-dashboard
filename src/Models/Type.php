<?php

namespace LighthouseDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    protected $table = 'ld_types';
    protected $guarded = ['id'];

    public function schema(): BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
