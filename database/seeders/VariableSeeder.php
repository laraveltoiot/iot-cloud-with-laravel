<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Thing;
use App\Models\Variable;
use Illuminate\Database\Seeder;

final class VariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all things or create some if none exist
        $things = Thing::all();
        if ($things->isEmpty()) {
            $this->command->info('No things found. Running ThingSeeder...');
            $this->call(ThingSeeder::class);
            $things = Thing::all();
        }

        // Create variables for each thing
        foreach ($things as $thing) {
            // Create 3-5 variables for each thing
            $numVariables = rand(3, 5);

            for ($i = 1; $i <= $numVariables; $i++) {
                $dataType = $this->getRandomDataType();

                Variable::create([
                    'thing_id' => $thing->id,
                    'name' => $this->getVariableName($dataType, $i),
                    'variable_id' => 'VAR-'.mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8)),
                    'data_type' => $dataType,
                    'description' => "This is a {$dataType} variable for {$thing->name}",
                    'unit' => $this->getUnitForDataType($dataType),
                    'metadata' => $this->getMetadataForDataType($dataType),
                    'current_value' => $this->getRandomValueForDataType($dataType),
                    'read_only' => rand(0, 3) === 0, // 25% chance of being read-only
                ]);
            }
        }
    }

    /**
     * Get a random data type.
     */
    private function getRandomDataType(): string
    {
        $dataTypes = ['int', 'float', 'string', 'boolean', 'color'];

        return $dataTypes[array_rand($dataTypes)];
    }

    /**
     * Get a variable name based on data type and index.
     */
    private function getVariableName(string $dataType, int $index): string
    {
        $prefixes = [
            'int' => ['counter', 'level', 'position', 'quantity', 'index'],
            'float' => ['temperature', 'humidity', 'pressure', 'voltage', 'current'],
            'string' => ['message', 'status', 'label', 'description', 'name'],
            'boolean' => ['isActive', 'isEnabled', 'isConnected', 'isOpen', 'isRunning'],
            'color' => ['ledColor', 'backgroundColor', 'textColor', 'indicatorColor', 'highlightColor'],
        ];

        $prefix = $prefixes[$dataType][($index - 1) % count($prefixes[$dataType])];

        return $prefix.ucfirst($dataType).$index;
    }

    /**
     * Get a unit for a data type.
     */
    private function getUnitForDataType(string $dataType): ?string
    {
        $units = [
            'int' => ['count', 'steps', 'units', 'items', null],
            'float' => ['°C', '%', 'hPa', 'V', 'A'],
            'string' => [null],
            'boolean' => [null],
            'color' => [null],
        ];

        return $units[$dataType][array_rand($units[$dataType])];
    }

    /**
     * Get metadata for a data type.
     */
    private function getMetadataForDataType(string $dataType): ?array
    {
        $metadata = [
            'int' => [
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ],
            'float' => [
                'min' => 0.0,
                'max' => 100.0,
                'precision' => 2,
            ],
            'string' => [
                'maxLength' => 255,
            ],
            'boolean' => [
                'trueLabel' => 'On',
                'falseLabel' => 'Off',
            ],
            'color' => [
                'format' => 'hex',
            ],
        ];

        return $metadata[$dataType];
    }

    /**
     * Get a random value for a data type.
     */
    private function getRandomValueForDataType(string $dataType): mixed
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
}
