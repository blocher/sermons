<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Sermon extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['delivered_on'];

    public function feast(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }
}
