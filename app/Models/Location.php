<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->subname) {
            $name = $this->name . ', ' . $this->subname;
        }

        return "$name ($this->city, $this->state)";
    }
}
