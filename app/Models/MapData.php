<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapData extends Model
{
    protected $fillable = ['visualization_id', 'region_id', 'region_name', 'variable_name', 'variable_value'];

    public function visualization(): BelongsTo
    {
        return $this->belongsTo(Visualization::class);
    }
}