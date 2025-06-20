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
        Schema::create('triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('source_variable_id')->nullable()->constrained('variables')->onDelete('cascade');
            $table->string('condition_type'); // e.g., greater_than, less_than, equal_to, etc.
            $table->json('condition_value'); // The value to compare against
            $table->string('action_type'); // e.g., set_variable, send_notification, etc.
            $table->foreignId('target_variable_id')->nullable()->constrained('variables')->nullOnDelete();
            $table->json('action_parameters')->nullable(); // Parameters for the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triggers');
    }
};
