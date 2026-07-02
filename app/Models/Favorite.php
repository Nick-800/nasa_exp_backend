<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FavoriteType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'external_id', 'metadata'])]
final class Favorite extends Model
{
    protected function casts(): array
    {
        return [
            'type' => FavoriteType::class,
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
