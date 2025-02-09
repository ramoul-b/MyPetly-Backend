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
        Schema::table('animals', function (Blueprint $table) {
            $table->enum('sex', ['male', 'female'])->nullable()->after('name'); 
            $table->string('color')->nullable()->after('sex');
            $table->decimal('weight', 5, 2)->nullable()->after('color'); // Poids en kg
            $table->decimal('height', 5, 2)->nullable()->after('weight'); // Taille en cm
            $table->string('identification_number')->nullable()->unique()->after('height'); // Numéro de puce ou tatouage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn(['sex', 'color', 'weight', 'height', 'identification_number']);
        });
    }
};
