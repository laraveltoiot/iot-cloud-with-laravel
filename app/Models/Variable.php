<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variable extends Model
{
    use HasFactory;

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
        'metadata' => 'json',
        'current_value' => 'json',
        'read_only' => 'boolean',
    ];

    /**
     * Get the thing that owns the variable.
     */
    public function thing(): BelongsTo
    {
        return $this->belongsTo(Thing::class);
    }

    /**
     * Get the source triggers for the variable.
     */
    public function sourceTriggers(): HasMany
    {
        return $this->hasMany(Trigger::class, 'source_variable_id');
    }

    /**
     * Get the target triggers for the variable.
     */
    public function targetTriggers(): HasMany
    {
        return $this->hasMany(Trigger::class, 'target_variable_id');
    }

    /**
     * Get the dashboard widgets for the variable.
     */
    public function dashboardWidgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }
}
