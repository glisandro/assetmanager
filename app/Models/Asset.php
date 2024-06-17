<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use GalleryJsonMedia\JsonMedia\Concerns\InteractWithMedia;
use GalleryJsonMedia\JsonMedia\Contracts\HasMedia;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'attachment' => 'array',
        'images' => 'array',
        'documents' => 'array',
    ];

    public function building() : BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function attachment() : BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'assets_attachments', 'asset_id', 'attachable_id');
    }

 
}
