<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sketch;
use App\Models\User;
use Illuminate\Database\Seeder;

final class SketchSeeder extends Seeder
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

        foreach ($users as $user) {
            // Create 3 sketches for each user
            for ($i = 1; $i <= 3; $i++) {
                Sketch::create([
                    'user_id' => $user->id,
                    'name' => "Sketch $i for {$user->name}",
                    'sketch_id' => 'SKT-'.mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8)),
                    'description' => "This is a sample sketch $i for {$user->name}",
                    'code' => $this->getSampleCode($i),
                    'version' => '1.0.0',
                    'is_compiled' => true,
                    'last_compiled_at' => now(),
                    'compilation_result' => ['success' => true, 'message' => 'Compilation successful'],
                ]);
            }
        }
    }

    /**
     * Get sample Arduino code based on the sketch number.
     */
    private function getSampleCode(int $sketchNumber): string
    {
        $sampleCodes = [
            // Basic LED blink sketch
            "void setup() {\n  pinMode(LED_BUILTIN, OUTPUT);\n}\n\nvoid loop() {\n  digitalWrite(LED_BUILTIN, HIGH);\n  delay(1000);\n  digitalWrite(LED_BUILTIN, LOW);\n  delay(1000);\n}",

            // Temperature sensor sketch
            "const int sensorPin = A0;\n\nvoid setup() {\n  Serial.begin(9600);\n}\n\nvoid loop() {\n  int sensorVal = analogRead(sensorPin);\n  float voltage = (sensorVal/1024.0) * 5.0;\n  float temperature = (voltage - 0.5) * 100;\n  Serial.print(\"Temperature: \");\n  Serial.print(temperature);\n  Serial.println(\" °C\");\n  delay(1000);\n}",

            // Motion detector sketch
            "const int pirPin = 2;\nconst int ledPin = 13;\n\nvoid setup() {\n  pinMode(pirPin, INPUT);\n  pinMode(ledPin, OUTPUT);\n  Serial.begin(9600);\n}\n\nvoid loop() {\n  int pirState = digitalRead(pirPin);\n  if (pirState == HIGH) {\n    digitalWrite(ledPin, HIGH);\n    Serial.println(\"Motion detected!\");\n  } else {\n    digitalWrite(ledPin, LOW);\n  }\n  delay(100);\n}",
        ];

        // Use modulo to cycle through the sample codes
        return $sampleCodes[($sketchNumber - 1) % count($sampleCodes)];
    }
}
