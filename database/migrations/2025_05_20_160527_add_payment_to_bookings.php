<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $t) {
            // lien vers le vétérinaire / prestataire
            $t->foreignId('provider_id')->after('service_id')->constrained('providers');

            // créneau horaire HH:MM
            $t->string('time', 5)->after('appointment_date');

            // Stripe
            $t->string('payment_intent', 60)->after('time');
            $t->string('currency', 3)->default('eur')->after('payment_intent');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropColumn(['provider_id', 'time', 'payment_intent', 'currency']);
        });
    }
};
