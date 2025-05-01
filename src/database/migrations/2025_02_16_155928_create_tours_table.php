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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();

            $table->text('title');
            $table->text('name');
            $table->string('slug')->unique();

            $table->string('short')->nullable();
            $table->longText('body')->nullable();

            $table->json('highlights')->nullable();
            $table->json('included')->nullable();
            $table->json('not_included')->nullable();
            $table->json('know_before')->nullable();

            $table->text('cities')->nullable()->comment('The selected cities');
            $table->text('paths')->nullable()->comment('The selected paths');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->string('type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
