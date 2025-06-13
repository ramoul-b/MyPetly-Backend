<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->json('name'); // Multilingue
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->json('description')->nullable(); // Multilingue
            $table->string('photo')->nullable();
            $table->integer('birth_year')->nullable();
            $table->json('specialization')->nullable(); // Multilingue
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->text('personal_info')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('providers');
    }
};
