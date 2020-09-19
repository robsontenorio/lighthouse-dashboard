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

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

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

    public static function topIn(array $range)
    {
        return Operation::query()
            ->with('field')
            ->withCount(['requests as total_requests' => function ($query) use ($range) {
                return $query->isOperation()->inRange($range);
            }])
            ->orderByDesc('total_requests')
            ->take(10)
            ->get();
    }

    public static function slowIn(array $range)
    {
        return Operation::query()
            ->with('field')
            ->whereHas('requests', function ($query) use ($range) {
                return $query->isOperation()->inRange($range);
            })
            ->get()
            ->map(function ($operation) use ($range) {
                // TODO
                $operation->average_duration = $operation->getAverageDurationIn($range);
                $operation->latest_duration = $operation->getLatestDurationIn($range);

                return $operation;
            })
            ->sortByDesc('average_duration')
            ->take(10)
            ->values();
    }

    public function sumaryWithClients(Operation $operation, array $range)
    {
        return Client::query()
            ->whereHas('requests', function ($query) use ($operation, $range) {
                $query->forOperation($operation)->inRange($range);
            })
            ->withCount(['requests as total_requests' => function ($query) use ($operation, $range) {
                $query->forOperation($operation)->inRange($range);
            }])
            ->get();
    }

    private function getAverageDurationIn(array $range)
    {
        $average = $this->requests()
            ->inRange($range)
            ->isOperation()
            ->avg('duration');

        return floor($average / 1000000);
    }

    private function getLatestDurationIn(array $range)
    {
        $latest = $this->requests()
            ->isOperation()
            ->latest('requested_at')
            ->first()
            ->duration;

        return floor($latest / 1000000);
    }
}
