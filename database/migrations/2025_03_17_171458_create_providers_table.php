<?php

// database/migrations/xxxx_xx_xx_create_providers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->integer('birth_year')->nullable();
            $table->string('specialization')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->text('personal_info')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('providers');
    }
};
