<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tracing extends Model
{
    use HasFactory;

    protected $table = 'ld_tracings';
    protected $guarded = ['id'];
    protected $casts = [
        'execution' => 'array',
    ];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}
