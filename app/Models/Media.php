<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    public const PERMISSION_GRANTED = 'granted';

    protected $table = 'media';

    protected $guarded = ['id'];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function culturalEntry(): BelongsTo
    {
        return $this->belongsTo(CulturalEntry::class, 'cultural_entry_id');
    }
}
