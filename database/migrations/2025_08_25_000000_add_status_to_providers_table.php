<?php

use App\Enums\ProviderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->string('status', 50)->default(ProviderStatusEnum::PENDING->value)->after('rating');
            $table->timestamp('validated_at')->nullable()->after('status');
        });

        DB::table('providers')
            ->whereNull('status')
            ->update([
                'status' => ProviderStatusEnum::PENDING->value,
            ]);
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['validated_at', 'status']);
        });
    }
};
