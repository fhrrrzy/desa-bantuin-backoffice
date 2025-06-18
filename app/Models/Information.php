<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Information extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'laporan_type_id',
    ];

    /**
     * Get the laporan type that owns the information.
     */
    public function laporanType(): BelongsTo
    {
        return $this->belongsTo(LaporanType::class);
    }
}
