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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('program_name');
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->foreignId('program_type_id')->constrained('program_types')->restrictOnDelete();
            $table->string('degree_coursework_name')->nullable();
            $table->string('program_major')->nullable();
            $table->foreignId('college_id')->constrained('colleges')->restrictOnDelete();
            $table->enum('program_format', ['asynchronous', 'synchronous', 'mixed', 'hybrid']);
            $table->string('duration')->nullable();
            $table->integer('credit_hours')->nullable();
            $table->decimal('program_fees', 10, 2)->nullable();
            $table->longText('overview')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
