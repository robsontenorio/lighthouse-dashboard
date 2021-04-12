<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Error extends Model
{
    use HasFactory;

    protected $table = 'ld_errors';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public static function latestIn(array $range, array $selectedClients)
    {
        return Error::query()
            ->with(['client', 'operation.field'])
            ->whereBetween('requested_at', $range)
            ->whereIn('client_id', $selectedClients)
            ->latest()
            ->take(100)
            ->get();
    }
}
