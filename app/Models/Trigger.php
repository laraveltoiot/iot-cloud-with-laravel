<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trigger extends Model
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
        'active' => 'boolean',
        'condition_value' => 'json',
        'action_parameters' => 'json',
    ];

    /**
     * Get the user that owns the trigger.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source variable for the trigger.
     */
    public function sourceVariable(): BelongsTo
    {
        return $this->belongsTo(Variable::class, 'source_variable_id');
    }

    /**
     * Get the target variable for the trigger.
     */
    public function targetVariable(): BelongsTo
    {
        return $this->belongsTo(Variable::class, 'target_variable_id');
    }
}
