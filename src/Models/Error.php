<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Error extends Model
{
    use HasFactory;

    protected $table = 'ld_errors';
    protected $guarded = ['id'];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public static function latestIn(array $range, array $selectedClients)
    {
        return Error::query()
            ->with(['request.client', 'request.operation.field'])
            ->whereHas('request', function ($query) use ($range, $selectedClients) {
                $query->inRange($range)->forClients($selectedClients);
            })
            ->latest()
            ->take(100)
            ->get();
    }
}
