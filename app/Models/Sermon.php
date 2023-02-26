<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Sermon extends Model
{
    use HasFactory;
    use Searchable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'delivered_on' => 'datetime:Y-m-d'
    ];

    public function feast(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getReadingsArrayAttribute()
    {
        return array_map("trim", explode('; ', $this->readings));
    }
}
