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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->after('service_id')->constrained('providers');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('appointment_date');
            $table->string('time', 5)->after('appointment_date');
            $table->string('payment_intent', 60)->after('time');
            $table->string('currency', 3)->default('eur')->after('payment_intent');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['provider_id', 'time', 'payment_intent', 'currency']);
        });

        Schema::dropIfExists('bookings');
    }
};
