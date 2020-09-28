<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $table = 'ld_fields';
    protected $guarded = ['id'];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function sumaryWithClients(array $range)
    {
        return  Client::all()
            ->map(function ($client) use ($range) {
                $client->metrics =  Operation::query()
                    ->with('field')
                    ->whereHas('requests', function ($query) use ($client, $range) {
                        $query->forClient($client)->forField($this)->inRange($range);
                    })
                    ->withCount(['requests as total_requests' => function (Builder $query) use ($client, $range) {
                        $query->forClient($client)->forField($this)->inRange($range);
                    }])
                    ->get();

                return $client;
            })
            ->reject(fn ($client) => count($client->metrics) == 0)
            ->values();
    }
}
