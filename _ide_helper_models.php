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
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_public
 * @property bool $is_default
 * @property array<array-key, mixed>|null $layout_config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DashboardWidget> $widgets
 * @property-read int|null $widgets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereLayoutConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dashboard whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDashboard {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $dashboard_id
 * @property string $name
 * @property string $widget_type
 * @property int|null $variable_id
 * @property int $position_x
 * @property int $position_y
 * @property int $width
 * @property int $height
 * @property array<array-key, mixed>|null $widget_config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Dashboard $dashboard
 * @property-read \App\Models\Variable|null $variable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereDashboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget wherePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget wherePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereVariableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereWidgetConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereWidgetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DashboardWidget whereWidth($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDashboardWidget {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $device_id
 * @property string|null $type
 * @property string $status
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thing> $things
 * @property-read int|null $things_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperDevice {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $thing_id
 * @property string|null $description
 * @property array<array-key, mixed>|null $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Variable> $variables
 * @property-read int|null $variables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereThingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thing whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperThing {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property int|null $source_variable_id
 * @property string $condition_type
 * @property array<array-key, mixed> $condition_value
 * @property string $action_type
 * @property int|null $target_variable_id
 * @property array<array-key, mixed>|null $action_parameters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Variable|null $sourceVariable
 * @property-read \App\Models\Variable|null $targetVariable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereActionParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereConditionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereConditionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereSourceVariableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereTargetVariableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trigger whereUserId($value)
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
 * @property int $id
 * @property int $thing_id
 * @property string $name
 * @property string $variable_id
 * @property string $data_type
 * @property string|null $description
 * @property string|null $unit
 * @property array<array-key, mixed>|null $metadata
 * @property array<array-key, mixed>|null $current_value
 * @property bool $read_only
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DashboardWidget> $dashboardWidgets
 * @property-read int|null $dashboard_widgets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trigger> $sourceTriggers
 * @property-read int|null $source_triggers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trigger> $targetTriggers
 * @property-read int|null $target_triggers_count
 * @property-read \App\Models\Thing $thing
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereCurrentValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereReadOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereThingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Variable whereVariableId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperVariable {}
}

