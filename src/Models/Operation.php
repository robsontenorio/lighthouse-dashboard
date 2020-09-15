<?php

namespace LighthouseDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    protected $table = 'ld_operations';
    protected $guarded = ['id'];

    public function tracings(): HasMany
    {
        return $this->hasMany(Tracing::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(FieldsOperations::class);
    }
}
