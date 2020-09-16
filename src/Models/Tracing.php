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
        'request' => 'array',
        'execution' => 'array'
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }
}
