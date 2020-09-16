<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $table = 'ld_fields';
    protected $guarded = ['id'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(FieldsOperations::class);
    }
}
