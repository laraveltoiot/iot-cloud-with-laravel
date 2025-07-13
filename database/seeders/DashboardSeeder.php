<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Dashboard;
use App\Models\DashboardWidget;
use App\Models\User;
use App\Models\Variable;
use Illuminate\Database\Seeder;

final class DashboardSeeder extends Seeder
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

        // Create dashboards for each user
        foreach ($users as $user) {
            // Get variables from things owned by this user
            $userVariables = $variables->filter(function ($variable) use ($user) {
                return $variable->thing->user_id === $user->id;
            });

            if ($userVariables->isEmpty()) {
                continue;
            }

            // Create 2 dashboards for each user
            for ($i = 1; $i <= 2; $i++) {
                $dashboard = Dashboard::create([
                    'user_id' => $user->id,
                    'name' => $i === 1 ? "Main Dashboard for {$user->name}" : "Secondary Dashboard for {$user->name}",
                    'description' => $i === 1 ? 'Primary dashboard with key metrics' : 'Additional monitoring dashboard',
                    'is_public' => $i === 1, // First dashboard is public, second is private
                    'is_default' => $i === 1, // First dashboard is default
                    'layout_config' => [
                        'columns' => 12,
                        'rowHeight' => 50,
                        'background' => $i === 1 ? '#f5f5f5' : '#e0f7fa',
                    ],
                ]);

                // Add widgets to the dashboard
                $this->createWidgetsForDashboard($dashboard, $userVariables);
            }
        }
    }

    /**
     * Create widgets for a dashboard.
     */
    private function createWidgetsForDashboard(Dashboard $dashboard, $variables): void
    {
        // Determine how many widgets to create (between 4 and 8)
        $numWidgets = rand(4, 8);

        // Keep track of positions to avoid overlap
        $positions = [];

        // Create widgets
        for ($i = 1; $i <= $numWidgets; $i++) {
            // Get a random variable if there are any left
            if ($variables->isEmpty()) {
                break;
            }

            $variable = $variables->random();

            // Determine widget type based on variable data type
            $widgetType = $this->getWidgetTypeForDataType($variable->data_type);

            // Determine widget size
            $width = rand(2, 6);
            $height = rand(2, 4);

            // Find a position that doesn't overlap with existing widgets
            $position = $this->findAvailablePosition($positions, $width, $height);

            // Create the widget
            DashboardWidget::create([
                'dashboard_id' => $dashboard->id,
                'name' => "{$variable->name} Widget",
                'widget_type' => $widgetType,
                'variable_id' => $variable->id,
                'position_x' => $position['x'],
                'position_y' => $position['y'],
                'width' => $width,
                'height' => $height,
                'widget_config' => $this->getWidgetConfigForType($widgetType, $variable),
            ]);

            // Add the position to the list of used positions
            $positions[] = [
                'x' => $position['x'],
                'y' => $position['y'],
                'width' => $width,
                'height' => $height,
            ];

            // Remove the variable from the collection to avoid duplicates
            $variables = $variables->except($variable->id);
        }
    }

    /**
     * Get a widget type based on data type.
     */
    private function getWidgetTypeForDataType(string $dataType): string
    {
        $widgetTypes = [
            'int' => ['gauge', 'value', 'chart'],
            'float' => ['gauge', 'value', 'chart'],
            'string' => ['value', 'text'],
            'boolean' => ['switch', 'led'],
            'color' => ['color-picker', 'value'],
        ];

        return $widgetTypes[$dataType][array_rand($widgetTypes[$dataType])];
    }

    /**
     * Find an available position for a widget.
     */
    private function findAvailablePosition(array $positions, int $width, int $height): array
    {
        $maxX = 12 - $width; // Maximum X position (12-column grid)
        $maxY = 20; // Maximum Y position

        $attempts = 0;
        $maxAttempts = 100;

        while ($attempts < $maxAttempts) {
            $x = rand(0, $maxX);
            $y = rand(0, $maxY);

            $overlaps = false;

            foreach ($positions as $position) {
                if (
                    $x < ($position['x'] + $position['width']) &&
                    ($x + $width) > $position['x'] &&
                    $y < ($position['y'] + $position['height']) &&
                    ($y + $height) > $position['y']
                ) {
                    $overlaps = true;
                    break;
                }
            }

            if (! $overlaps) {
                return ['x' => $x, 'y' => $y];
            }

            $attempts++;
        }

        // If we couldn't find a non-overlapping position, just return a position at the bottom
        $maxY = 0;

        foreach ($positions as $position) {
            $maxY = max($maxY, $position['y'] + $position['height']);
        }

        return ['x' => rand(0, $maxX), 'y' => $maxY];
    }

    /**
     * Get widget configuration based on widget type and variable.
     */
    private function getWidgetConfigForType(string $widgetType, Variable $variable): array
    {
        $config = [
            'title' => $variable->name,
            'showTitle' => true,
            'unit' => $variable->unit,
        ];

        switch ($widgetType) {
            case 'gauge':
                $config['min'] = 0;
                $config['max'] = 100;
                $config['thresholds'] = [
                    ['value' => 30, 'color' => 'green'],
                    ['value' => 70, 'color' => 'yellow'],
                    ['value' => 100, 'color' => 'red'],
                ];
                break;

            case 'chart':
                $config['chartType'] = ['line', 'bar', 'area'][array_rand(['line', 'bar', 'area'])];
                $config['timeRange'] = ['1h', '6h', '24h', '7d'][array_rand(['1h', '6h', '24h', '7d'])];
                $config['showLegend'] = (bool) rand(0, 1);
                break;

            case 'switch':
                $config['onLabel'] = 'ON';
                $config['offLabel'] = 'OFF';
                $config['onColor'] = '#4CAF50';
                $config['offColor'] = '#F44336';
                break;

            case 'led':
                $config['onColor'] = '#4CAF50';
                $config['offColor'] = '#F44336';
                $config['size'] = ['small', 'medium', 'large'][array_rand(['small', 'medium', 'large'])];
                break;

            case 'color-picker':
                $config['format'] = 'hex';
                $config['showAlpha'] = false;
                break;
        }

        return $config;
    }
}
