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
        Schema::create('sketches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sketch_id')->unique(); // Unique identifier for the sketch
            $table->text('description')->nullable();
            $table->text('code')->nullable(); // The actual sketch code
            $table->string('version')->default('1.0.0'); // Version of the sketch
            $table->timestamp('last_compiled_at')->nullable(); // When the sketch was last compiled
            $table->boolean('is_compiled')->default(false); // Whether the sketch is compiled
            $table->json('compilation_result')->nullable(); // Result of the compilation
            $table->timestamps();
        });

        // Add foreign key constraint to things table
        Schema::table('things', function (Blueprint $table) {
            $table->foreign('sketch_id')->references('id')->on('sketches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraint from things table
        Schema::table('things', function (Blueprint $table) {
            $table->dropForeign(['sketch_id']);
        });

        Schema::dropIfExists('sketches');
    }
};
