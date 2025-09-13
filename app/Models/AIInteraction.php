<?php
// app/Models/AIInteraction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $prompt_summary
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction successful()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction today()
 * @mixin \Eloquent
 */
class AIInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'status',
        'response_time_ms',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les interactions réussies.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope pour les interactions d'aujourd'hui.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Accesseur pour obtenir un résumé du prompt.
     */
    public function getPromptSummaryAttribute(): string
    {
        return strlen($this->prompt) > 100
            ? substr($this->prompt, 0, 97) . '...'
            : $this->prompt;
    }
}
