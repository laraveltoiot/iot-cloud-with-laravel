<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperThing
 */
final class Thing extends Model
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
        'properties' => 'json',
        'tags' => 'json',
        'network_config' => 'json',
    ];

    /**
     * Get the user that owns the thing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the devices associated with the thing.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'thing_device')
            ->withPivot('config')
            ->withTimestamps();
    }

    /**
     * Get the variables for the thing.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(Variable::class);
    }

    /**
     * Get the sketch associated with the thing.
     */
    public function sketch(): BelongsTo
    {
        return $this->belongsTo(Sketch::class);
    }
}
