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

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
