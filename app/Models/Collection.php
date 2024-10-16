<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    /** @use HasFactory<\Database\Factories\CollectionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id'];

    /**
     * @return BelongsTo<User, Collection>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany<Media>
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'collection_media');
    }
}
