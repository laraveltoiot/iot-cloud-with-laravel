<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Sketch;
use App\Models\Thing;
use App\Models\User;
use Illuminate\Database\Seeder;

final class ThingSeeder extends Seeder
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

        // Get all sketches or create some if none exist
        $sketches = Sketch::all();
        if ($sketches->isEmpty()) {
            $this->command->info('No sketches found. Running SketchSeeder...');
            $this->call(SketchSeeder::class);
            $sketches = Sketch::all();
        }

        // Get all devices or create some if none exist
        $devices = Device::all();
        if ($devices->isEmpty()) {
            $this->command->info('No devices found. Creating some devices...');
            foreach ($users as $user) {
                for ($i = 1; $i <= 3; $i++) {
                    Device::create([
                        'user_id' => $user->id,
                        'name' => "Device $i for {$user->name}",
                        'device_id' => 'DEV-'.mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8)),
                        'type' => $this->getRandomDeviceType(),
                        'status' => $this->getRandomStatus(),
                        'metadata' => ['manufacturer' => 'Arduino', 'model' => 'Uno'],
                    ]);
                }
            }
            $devices = Device::all();
        }

        // Create things for each user
        foreach ($users as $user) {
            // Get sketches for this user
            $userSketches = $sketches->where('user_id', $user->id);

            // Get devices for this user
            $userDevices = $devices->where('user_id', $user->id);

            // Create 3 things for each user
            for ($i = 1; $i <= 3; $i++) {
                $thing = Thing::create([
                    'user_id' => $user->id,
                    'name' => "Thing $i for {$user->name}",
                    'thing_id' => 'THG-'.mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8)),
                    'description' => "This is a sample thing $i for {$user->name}",
                    'properties' => ['category' => $this->getRandomCategory()],
                    'timezone' => $this->getRandomTimezone(),
                    'tags' => $this->getRandomTags(),
                    'network_config' => $this->getRandomNetworkConfig(),
                    'sketch_id' => $userSketches->isNotEmpty() ? $userSketches->random()->id : null,
                    'status' => $this->getRandomStatus(),
                ]);

                // Attach random devices to this thing
                if ($userDevices->isNotEmpty()) {
                    $randomDevices = $userDevices->random(min(2, $userDevices->count()));
                    foreach ($randomDevices as $device) {
                        $thing->devices()->attach($device->id, [
                            'config' => json_encode(['pin_mapping' => $this->getRandomPinMapping()]),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Get a random device type.
     */
    private function getRandomDeviceType(): string
    {
        $types = ['Arduino Uno', 'Arduino Mega', 'ESP8266', 'ESP32', 'Raspberry Pi Pico'];

        return $types[array_rand($types)];
    }

    /**
     * Get a random status.
     */
    private function getRandomStatus(): string
    {
        $statuses = ['online', 'offline', 'error'];

        return $statuses[array_rand($statuses)];
    }

    /**
     * Get a random category.
     */
    private function getRandomCategory(): string
    {
        $categories = ['Home Automation', 'Weather Station', 'Security System', 'Garden Monitor', 'Energy Monitor'];

        return $categories[array_rand($categories)];
    }

    /**
     * Get a random timezone.
     */
    private function getRandomTimezone(): string
    {
        $timezones = ['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo', 'Australia/Sydney'];

        return $timezones[array_rand($timezones)];
    }

    /**
     * Get random tags.
     */
    private function getRandomTags(): array
    {
        $allTags = ['home', 'office', 'outdoor', 'indoor', 'temperature', 'humidity', 'motion', 'light', 'energy'];
        $numTags = rand(1, 3);
        $tags = [];

        for ($i = 0; $i < $numTags; $i++) {
            $tags[] = $allTags[array_rand($allTags)];
        }

        return array_unique($tags);
    }

    /**
     * Get random network configuration.
     */
    private function getRandomNetworkConfig(): array
    {
        return [
            'wifi' => [
                'ssid' => 'Sample_WiFi_'.rand(1, 100),
                'password' => 'password'.rand(1000, 9999),
                'security' => 'WPA2',
            ],
            'ip_config' => [
                'type' => rand(0, 1) ? 'static' : 'dhcp',
                'ip' => rand(0, 1) ? '192.168.1.'.rand(2, 254) : null,
                'gateway' => rand(0, 1) ? '192.168.1.1' : null,
                'subnet' => rand(0, 1) ? '255.255.255.0' : null,
            ],
        ];
    }

    /**
     * Get random pin mapping for device-thing connection.
     */
    private function getRandomPinMapping(): array
    {
        $pins = [];
        $numPins = rand(1, 5);

        for ($i = 0; $i < $numPins; $i++) {
            $pins['pin_'.rand(0, 13)] = $this->getRandomPinFunction();
        }

        return $pins;
    }

    /**
     * Get a random pin function.
     */
    private function getRandomPinFunction(): string
    {
        $functions = ['digital_input', 'digital_output', 'analog_input', 'analog_output', 'pwm', 'i2c_sda', 'i2c_scl', 'spi_mosi', 'spi_miso', 'spi_sck'];

        return $functions[array_rand($functions)];
    }
}
