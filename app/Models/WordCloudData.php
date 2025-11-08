<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordCloudData extends Model
{
    protected $table = 'wordcloud_data';
    
    protected $fillable = ['visualization_id', 'word', 'value'];

    public function visualization(): BelongsTo
    {
        return $this->belongsTo(Visualization::class);
    }
}