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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('sex', ['male', 'female'])->nullable()->after('name');
            $table->string('color')->nullable()->after('sex');
            $table->decimal('weight', 5, 2)->nullable()->after('color');
            $table->decimal('height', 5, 2)->nullable()->after('weight');
            $table->string('identification_number')->nullable()->unique()->after('height');
            $table->string('species')->nullable();
            $table->string('breed')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'lost'])->default('active');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropUnique(['identification_number']);
            $table->dropColumn(['sex', 'color', 'weight', 'height', 'identification_number']);
        });

        Schema::dropIfExists('animals');
    }
};
