<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'ld_operations';
    protected $guarded = ['id'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function tracings(): HasMany
    {
        return $this->hasMany(Tracing::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(FieldsOperations::class);
    }

    public static function top(array $range)
    {
        return self::query()
            ->with('field')
            ->withCount(['tracings' => function ($query) use ($range) {
                return $query->whereBetween('created_at', $range);
            }])
            ->orderByDesc('tracings_count')
            ->get();
    }

    public static function slow(array $range)
    {
        return self::query()
            ->with('field')
            ->whereHas('tracings', function ($query) use ($range) {
                return $query->whereBetween('created_at', $range);
            })
            ->get()
            ->map(function ($operation) use ($range) {
                $operation->average_duration =  floor($operation->tracings()->whereBetween('created_at', $range)->avg('duration') / 1000000);
                $operation->latest_duration = floor($operation->tracings()->latest()->first()->duration / 1000000);

                return $operation;
            })
            ->sortByDesc('average_duration')
            ->values();
    }
}
