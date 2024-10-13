<?php

namespace App\Models;

use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'media', 'description', 'type'];

    protected $casts = [
        'type' => MediaType::class,
    ];

    /**
     * @return BelongsTo<User, Media>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
