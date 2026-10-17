
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('claimed_by')->nullable();
            $table->timestamp('claimed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn([
                'active',
                'claimed_by',
                'claimed_at',
            ]);
        });
    }
};
