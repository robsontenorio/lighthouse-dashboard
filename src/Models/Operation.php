<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function tracings(): HasManyThrough
    {
        return $this->hasManyThrough(Tracing::class, Request::class);
    }

    public static function top(array $range)
    {
        return self::query()
            ->with('field')
            ->withCount(['requests as total_requests' => function ($query) use ($range) {
                return $query->whereBetween('requested_at', $range)->whereNotNull('duration');
            }])
            ->orderByDesc('total_requests')
            ->take(10)
            ->get();
    }

    public static function slow(array $range)
    {
        return self::query()
            ->with('field')
            ->whereHas('requests', function ($query) use ($range) {
                return $query->whereBetween('requested_at', $range)->whereNotNull('duration');
            })
            ->get()
            ->map(function ($operation) use ($range) {
                // TODO
                $operation->average_duration = $operation->getOperationAverageDuration($range);
                $operation->latest_duration = $operation->getOperationLatestDuration($range);

                return $operation;
            })
            ->sortByDesc('average_duration')
            ->take(10)
            ->values();
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

    private function getOperationAverageDuration(array $range)
    {
        $average = $this->requests()
            ->whereBetween('requested_at', $range)
            ->whereNotNull('duration')
            ->avg('duration');

        return floor($average / 1000000);
    }

    private function getOperationLatestDuration(array $range)
    {
        $latest = $this->requests()
            ->whereNotNull('duration')
            ->latest('requested_at')
            ->first()
            ->duration;

        return floor($latest / 1000000);
    }
}
