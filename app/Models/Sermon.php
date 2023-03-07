<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        return array_map("trim", explode('; ', $this->readings_string));
    }

    public function atLeastOneSameReading(): array
    {
        return Sermon::whereHas('readings', function ($query) {
            $query->whereIn('id', $this->readings->pluck('id'));
        })->where('sermons.id', '!=', $this->id)->get()->toArray();
    }

    // Write a function that returns an array of sermons that have at least one of the same readings as this sermon

    public function sameReadings(): array
    {
        return Sermon::whereHas('readings', function ($query) {
            $query->whereIn('readings.id', $this->readings->pluck('id'));
        })->where('sermons.id', '!=', $this->id)->get()->toArray();
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $array['id'] = intval($array['id']);
        unset($array['created_at']);
        $array['location'] = [
            'id' => intval($this->location->id),
            'name' => $this->location->name,
            'subname' => $this->location->subname,
            'city' => $this->location->city,
            'state' => $this->location->state,
            'country' => $this->location->country,
            'website' => $this->location->webiste,
            'diocese' => $this->location->diocese,
            'displayName' => $this->location->displayName,
        ];
        $array['feast'] = [
            'id' => intval($this->feast->id),
            'name' => $this->feast->name,
            'color' => $this->feast->color,
            'handle' => $this->feast->handle,
            'rank' => $this->feast->rank,
            'priority' => intval($this->feast->priority),
        ];
        $array['proper'] = intval($array['proper']);
        unset($array['file']);
        unset($array['markup']);
        $readings = $this->readings;
        $array['readings'] = array_map(function ($reading) {
            $reading = Reading::where('id', $reading['id'])->first();
            return $reading->toSearchableArray();
        }, $readings->toArray());
        return $array;
    }

    public function readings(): BelongsToMany
    {
        return $this->belongsToMany(Reading::class, "sermon_reading");
    }
}
