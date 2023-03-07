<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reading extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sermons(): BelongsToMany
    {
        return $this->belongsToMany(Sermon::class, "sermon_reading");
    }

    public function noHeadings($translation = "nrsv"): string
    {
        return preg_replace('/<h[1-6]>.*?<\/h[1-6]>/', '', $this->$translation);
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['id'] = intval($this->id);
        unset($array['created_at']);
        unset($array['rsv']);
        unset($array['kjv']);
        return $array;
    }
}
