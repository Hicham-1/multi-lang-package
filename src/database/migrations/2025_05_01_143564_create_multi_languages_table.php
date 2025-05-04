<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('multi_languages')) {
            echo "\n⛔ Table 'multi_languages' already exists. Skipping migration.\n";
            return;
        }

        Schema::create('multi_languages', function (Blueprint $table) {
            $table->id();
            $table->string('language', 4);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        $nowDate = now();
        // Insert initial data
        DB::table('multi_languages')->insert([
            ['language' => 'en', 'is_default' => true, 'created_at' => $nowDate, 'updated_at' => $nowDate],
            ['language' => 'fr', 'is_default' => false, 'created_at' => $nowDate, 'updated_at' => $nowDate],
            ['language' => 'ar', 'is_default' => false, 'created_at' => $nowDate, 'updated_at' => $nowDate],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multi_languages');
    }
};
