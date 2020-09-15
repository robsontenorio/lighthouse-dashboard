<?php

namespace LighthouseDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schema extends Model
{
    protected $table = 'ld_schemas';
    protected $guarded = ['id'];

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }
}
