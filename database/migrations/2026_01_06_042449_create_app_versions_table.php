<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_number'); // e.g., "2.3.5"
            $table->date('release_date');
            $table->json('release_notes')->nullable(); // Storing bullet points as JSON array
            $table->boolean('is_mandatory')->default(false);
            $table->string('platform')->nullable(); // 'android', 'ios'
            $table->string('download_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
