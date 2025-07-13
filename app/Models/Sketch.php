<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSketch
 */
final class Sketch extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'compilation_result' => 'json',
        'is_compiled' => 'boolean',
        'last_compiled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the sketch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the things that use this sketch.
     */
    public function things(): HasMany
    {
        return $this->hasMany(Thing::class);
    }
}
