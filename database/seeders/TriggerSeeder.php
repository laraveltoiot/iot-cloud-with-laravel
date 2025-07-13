<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trigger;
use App\Models\User;
use App\Models\Variable;
use Illuminate\Database\Seeder;

final class TriggerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            // Create a default user if none exists
            $users = [User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ])];
        }

        // Get all variables or create some if none exist
        $variables = Variable::all();
        if ($variables->isEmpty()) {
            $this->command->info('No variables found. Running VariableSeeder...');
            $this->call(VariableSeeder::class);
            $variables = Variable::all();
        }

        // Create triggers for each user
        foreach ($users as $user) {
            // Get variables from things owned by this user
            $userVariables = $variables->filter(function ($variable) use ($user) {
                return $variable->thing->user_id === $user->id;
            });

            if ($userVariables->isEmpty()) {
                continue;
            }

            // Create 3-5 triggers for each user
            $numTriggers = rand(3, 5);

            for ($i = 1; $i <= $numTriggers; $i++) {
                // Get a random source variable
                $sourceVariable = $userVariables->random();

                // Determine condition type based on data type
                $conditionType = $this->getConditionTypeForDataType($sourceVariable->data_type);

                // Determine condition value based on data type
                $conditionValue = $this->getConditionValueForDataType($sourceVariable->data_type);

                // Determine action type
                $actionType = $this->getRandomActionType();

                // Get a random target variable if action type is 'set_variable'
                $targetVariableId = null;
                $actionParameters = null;

                if ($actionType === 'set_variable') {
                    // Filter variables that are not read-only and not the source variable
                    $potentialTargets = $userVariables->filter(function ($variable) use ($sourceVariable) {
                        return ! $variable->read_only && $variable->id !== $sourceVariable->id;
                    });

                    if ($potentialTargets->isNotEmpty()) {
                        $targetVariable = $potentialTargets->random();
                        $targetVariableId = $targetVariable->id;
                        $actionParameters = $this->getActionParametersForDataType($targetVariable->data_type);
                    } else {
                        // If no suitable target variable, change action type to notification
                        $actionType = 'send_notification';
                        $actionParameters = $this->getNotificationParameters();
                    }
                } else {
                    $actionParameters = $this->getNotificationParameters();
                }

                Trigger::create([
                    'user_id' => $user->id,
                    'name' => "Trigger $i for {$user->name}",
                    'description' => "This trigger monitors {$sourceVariable->name} and {$this->getActionDescription($actionType)}",
                    'active' => rand(0, 5) > 0, // 5/6 chance of being active
                    'source_variable_id' => $sourceVariable->id,
                    'condition_type' => $conditionType,
                    'condition_value' => $conditionValue,
                    'action_type' => $actionType,
                    'target_variable_id' => $targetVariableId,
                    'action_parameters' => $actionParameters,
                ]);
            }
        }
    }

    /**
     * Get a condition type based on data type.
     */
    private function getConditionTypeForDataType(string $dataType): string
    {
        $conditionTypes = [
            'int' => ['greater_than', 'less_than', 'equal_to', 'not_equal_to'],
            'float' => ['greater_than', 'less_than', 'equal_to', 'not_equal_to'],
            'string' => ['equal_to', 'not_equal_to', 'contains', 'starts_with', 'ends_with'],
            'boolean' => ['equal_to', 'not_equal_to'],
            'color' => ['equal_to', 'not_equal_to'],
        ];

        return $conditionTypes[$dataType][array_rand($conditionTypes[$dataType])];
    }

    /**
     * Get a condition value based on data type.
     */
    private function getConditionValueForDataType(string $dataType): mixed
    {
        switch ($dataType) {
            case 'int':
                return rand(0, 100);
            case 'float':
                return round(rand(0, 1000) / 10, 1);
            case 'string':
                $strings = ['Ready', 'Processing', 'Completed', 'Error', 'Waiting'];

                return $strings[array_rand($strings)];
            case 'boolean':
                return (bool) rand(0, 1);
            case 'color':
                $colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF'];

                return $colors[array_rand($colors)];
            default:
                return null;
        }
    }

    /**
     * Get a random action type.
     */
    private function getRandomActionType(): string
    {
        $actionTypes = ['set_variable', 'send_notification', 'send_email', 'webhook'];

        return $actionTypes[array_rand($actionTypes)];
    }

    /**
     * Get action parameters for a data type.
     */
    private function getActionParametersForDataType(string $dataType): array
    {
        switch ($dataType) {
            case 'int':
                return ['value' => rand(0, 100)];
            case 'float':
                return ['value' => round(rand(0, 1000) / 10, 1)];
            case 'string':
                $strings = ['Ready', 'Processing', 'Completed', 'Error', 'Waiting'];

                return ['value' => $strings[array_rand($strings)]];
            case 'boolean':
                return ['value' => (bool) rand(0, 1)];
            case 'color':
                $colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF'];

                return ['value' => $colors[array_rand($colors)]];
            default:
                return ['value' => null];
        }
    }

    /**
     * Get notification parameters.
     */
    private function getNotificationParameters(): array
    {
        return [
            'title' => 'Trigger Alert',
            'message' => 'Your trigger condition has been met!',
            'level' => ['info', 'warning', 'error'][array_rand(['info', 'warning', 'error'])],
        ];
    }

    /**
     * Get action description.
     */
    private function getActionDescription(string $actionType): string
    {
        switch ($actionType) {
            case 'set_variable':
                return 'sets another variable';
            case 'send_notification':
                return 'sends a notification';
            case 'send_email':
                return 'sends an email';
            case 'webhook':
                return 'calls a webhook';
            default:
                return 'performs an action';
        }
    }
}
