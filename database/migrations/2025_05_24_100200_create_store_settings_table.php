<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('currency')->default('EUR');
            $table->string('timezone')->default('Europe/Paris');
            $table->string('locale')->default('fr');
            $table->boolean('inventory_tracking')->default(true);
            $table->boolean('notifications_enabled')->default(true);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('store_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
