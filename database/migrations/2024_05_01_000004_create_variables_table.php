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
        Schema::create('variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thing_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('variable_id')->unique(); // Unique identifier for the variable
            $table->string('data_type'); // int, float, string, boolean, etc.
            $table->text('description')->nullable();
            $table->string('unit')->nullable(); // e.g., celsius, meters, etc.
            $table->json('metadata')->nullable(); // Additional metadata
            $table->json('current_value')->nullable(); // Current value of the variable
            $table->boolean('read_only')->default(false); // Whether the variable is read-only
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variables');
    }
};
