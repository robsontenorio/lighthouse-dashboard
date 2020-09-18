<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schema extends Model
{
    use HasFactory;

    protected $table = 'ld_schemas';
    protected $guarded = ['id'];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }
}
