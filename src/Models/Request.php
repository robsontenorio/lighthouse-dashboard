<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $table = 'ld_requests';
    protected $guarded = ['id'];
    public $timestamps = false;
    public $casts = [
        'y' => 'int'
    ];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function tracing(): HasOne
    {
        return $this->hasOne(Tracing::class);
    }

    public function errors(): HasMany
    {
        return $this->hasMany(Error::class);
    }

    public function scopeIsOperation(Builder $query): Builder
    {
        return $query->whereNotNull('duration');
    }

    public function scopeInRange(Builder $query, array $range): Builder
    {
        return $query->whereBetween('requested_at', $range);
    }

    public function scopeForOperation(Builder $query, Operation $operation): Builder
    {
        return $query->where('operation_id', $operation->id)->isOperation();
    }

    public function scopeForClient(Builder $query, Client $client): Builder
    {
        return $query->where('client_id', $client->id);
    }

    public function scopeForClients(Builder $query, array $clients = []): Builder
    {
        return $query->whereIn('client_id', $clients);
    }

    public function scopeForField(Builder $query, Field $field): Builder
    {
        return $query->where('field_id', $field->id);
    }

    public static function seriesIn(array $range, array $clients = [])
    {
        $requests_series = Request::query()
            ->selectRaw('DATE(requested_at) as x, count(*) as y')
            ->isOperation()
            ->inRange($range)
            ->forClients($clients)
            ->groupBy('x')
            ->orderBy('x')
            ->get();

        if ($requests_series->count() == 0) {
            return [];
        }

        // Fill empty dates in range
        $period = CarbonPeriod::between($requests_series->first()->x, $range['end_date']);
        $series = collect();

        foreach ($period as $date) {
            if ($item = $requests_series->firstWhere('x', $date->toDateString())) {
                $series->add($item);
                continue;
            }

            $series->add(['x' => $date->toDateString(), 'y' => null]);
        }

        return $series;
    }
}
