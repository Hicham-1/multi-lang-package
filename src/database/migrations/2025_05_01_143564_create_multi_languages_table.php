<?php

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
        if (Schema::hasTable('multi_languages')) {
            echo "⛔ Table 'multi_languages' already exists. Skipping migration.\n";
            return;
        }

        Schema::create('multi_languages', function (Blueprint $table) {
            $table->id();
            $table->string('language', 4);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multi_languages');
    }
};
