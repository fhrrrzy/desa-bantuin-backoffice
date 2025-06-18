<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the requests for this laporan type.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(UserRequest::class);
    }

    /**
     * Get the information for this laporan type.
     */
    public function information(): HasMany
    {
        return $this->hasMany(Information::class);
    }
}
