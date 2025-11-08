<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visualization extends Model
{
    protected $fillable = ['name', 'type', 'description', 'geojson_file'];

    public function mapData(): HasMany
    {
        return $this->hasMany(MapData::class);
    }

    public function wordCloudData(): HasMany
    {
        return $this->hasMany(WordCloudData::class);
    }

    public function getVariablesAttribute()
    {
        if ($this->type === 'map') {
            return $this->mapData()->distinct('variable_name')->pluck('variable_name');
        }
        return [];
    }
}