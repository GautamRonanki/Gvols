<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfi_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
$table->string('full_name', 255);
            $table->string('phone_number')->nullable();
$table->string('phone_number', 50)->nullable();
Consider using ->constrained('programs')->restrictOnDelete() or ->nullOnDelete() to prevent accidental data loss
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfi_submissions');
    }
};
