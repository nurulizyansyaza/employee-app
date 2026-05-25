<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('id', 11)->primary();
            $table->string('name', 30);
            $table->date('birthdate');
            $table->boolean('sex');
            $table->string('address', 200)->nullable();
            $table->decimal('salary', 12, 4);
            $table->string('nik', 10)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('entry_date')->nullable();
            $table->timestamp('update_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
