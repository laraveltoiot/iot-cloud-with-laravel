<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('things', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('thing_id')->unique(); // Unique identifier for the thing
            $table->text('description')->nullable();
            $table->json('properties')->nullable(); // Additional properties
            $table->string('timezone')->default('UTC'); // Timezone for the thing
            $table->json('tags')->nullable(); // Tags for categorizing the thing
            $table->json('network_config')->nullable(); // Network configuration (WiFi credentials, etc.)
            $table->foreignId('sketch_id')->nullable(); // Associated sketch
            $table->enum('status', ['online', 'offline', 'error'])->default('offline'); // Thing status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('things');
    }
};
