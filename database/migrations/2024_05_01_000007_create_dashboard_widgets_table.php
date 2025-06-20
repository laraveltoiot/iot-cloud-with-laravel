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
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('widget_type'); // e.g., chart, gauge, value, etc.
            $table->foreignId('variable_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('position_x')->default(0); // X position on the dashboard grid
            $table->integer('position_y')->default(0); // Y position on the dashboard grid
            $table->integer('width')->default(1); // Width in grid units
            $table->integer('height')->default(1); // Height in grid units
            $table->json('widget_config')->nullable(); // Configuration specific to the widget type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
