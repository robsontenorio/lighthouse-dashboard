<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'ld_clients';
    protected $guarded = ['id'];

    public function getConnectionName()
    {
        return config('lighthouse-dashboard.connection');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public static function seriesIn(array $range)
    {
        return Client::query()
            ->withCount(['requests as total_requests' => function ($query) use ($range) {
                $query->isOperation()->inRange($range);
            }])
            ->orderByDesc('total_requests')
            ->get()
            ->map(function ($item) {
                return ['x' => $item->username, 'y' => $item['total_requests']];
            });
    }
}
