<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Day extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function holiday_1(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }

    public function holiday_2(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }

    public function holiday_3(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }
}
