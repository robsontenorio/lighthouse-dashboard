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

    // TODO include "durationNotNull" because it will be always request for a operation
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function tracings(): HasManyThrough
    {
        return $this->hasManyThrough(Tracing::class, Request::class);
    }

    public function errors(): HasManyThrough
    {
        return $this->hasManyThrough(Error::class, Request::class);
    }

    public static function topIn(array $range, array $clients = [])
    {
        return Operation::query()
            ->with('field')
            ->withCount(['requests as total_requests' => function ($query) use ($range, $clients) {
                return $query->forClients($clients)->isOperation()->inRange($range);
            }])
            ->withCount(['requests as total_errors' => function ($query) use ($range, $clients) {
                return $query->forClients($clients)->isOperation()->inRange($range)->whereHas('errors');
            }])
            ->orderByDesc('total_requests')
            ->take(10)
            ->get();
    }

    public static function slowIn(array $range, array $clients = [])
    {
        return Operation::query()
            ->with('field')
            ->whereHas('requests', function ($query) use ($range, $clients) {
                return $query->forClients($clients)->isOperation()->inRange($range);
            })
            ->get()
            ->map(function (Operation $operation) use ($range, $clients) {
                // TODO
                $operation->average_duration = $operation->getAverageDurationIn($range, $clients);
                $operation->latest_duration = $operation->getLatestDurationIn($range, $clients);

                return $operation;
            })
            ->sortByDesc('average_duration')
            ->take(10)
            ->values();
    }

    public function sumaryWithClients(Operation $operation, array $range, array $clients = [])
    {
        return Client::query()
            ->whereHas('requests', function ($query) use ($operation, $range, $clients) {
                $query->forClients($clients)->forOperation($operation)->inRange($range);
            })
            ->withCount(['requests as total_requests' => function ($query) use ($operation, $range, $clients) {
                $query->forClients($clients)->forOperation($operation)->inRange($range);
            }])
            ->get();
    }

    private function getAverageDurationIn(array $range)
    {
        return (int) $this->requests()->inRange($range)->isOperation()->avg('duration');
    }

    private function getLatestDurationIn(array $range)
    {
        return (int) $this->requests()->inRange($range)->isOperation()->latest('requested_at')->first()->duration;
    }
}
