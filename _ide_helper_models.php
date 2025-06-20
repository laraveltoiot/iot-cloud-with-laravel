<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DashboardWidget> $widgets
 * @property-read int|null $widgets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDashboard {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Dashboard|null $dashboard
 * @property-read \App\Models\Variable|null $variable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDashboardWidget {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thing> $things
 * @property-read int|null $things_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDevice {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Variable> $variables
 * @property-read int|null $variables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperThing {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Variable|null $sourceVariable
 * @property-read \App\Models\Variable|null $targetVariable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperTrigger {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dashboard> $dashboards
 * @property-read int|null $dashboards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thing> $things
 * @property-read int|null $things_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trigger> $triggers
 * @property-read int|null $triggers_count
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperUser {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DashboardWidget> $dashboardWidgets
 * @property-read int|null $dashboard_widgets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trigger> $sourceTriggers
 * @property-read int|null $source_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trigger> $targetTriggers
 * @property-read int|null $target_triggers_count
 * @property-read \App\Models\Thing|null $thing
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperVariable {}
}

