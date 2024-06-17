<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use GalleryJsonMedia\JsonMedia\Concerns\InteractWithMedia;
use GalleryJsonMedia\JsonMedia\Contracts\HasMedia;

class Attachment extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function assets() : BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'assets_attachments', 'attachable_id', 'asset_id');
    }
}
