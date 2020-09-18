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

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public static function top(array $range)
    {
        return self::query()
            ->with('field')
            ->withCount(['requests as total_requests' => function ($query) use ($range) {
                return $query->whereBetween('requested_at', $range)->whereNotNull('duration');
            }])
            ->orderByDesc('total_requests')
            ->get();
    }

    public static function slow(array $range)
    {
        return self::query()
            ->with('field')
            ->whereHas('requests', function ($query) use ($range) {
                return $query->whereBetween('requested_at', $range)->whereNotNull('duration');
            })
            ->get();
        // ->map(function ($operation) use ($range) {
        //     $operation->average_duration =  floor($operation->tracings()->whereBetween('created_at', $range)->avg('duration') / 1000000);
        //     $operation->latest_duration = floor($operation->tracings()->latest()->first()->duration / 1000000);

        //     return $operation;
        // })
        // ->sortByDesc('average_duration')
        // ->values();
    }

    public function sumary(Operation $operation, array $range)
    {
        return Client::query()
            ->whereHas('requests', function ($query) use ($operation, $range) {
                $query->where('operation_id', $operation->id)->whereBetween('requested_at', $range)->whereNotNull('duration');
            })
            ->withCount(['requests as total_requests' => function ($query) use ($operation, $range) {
                $query->where('operation_id', $operation->id)->whereBetween('requested_at', $range)->whereNotNull('duration');
            }])
            ->get();
    }
}
