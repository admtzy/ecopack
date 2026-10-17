
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partnership_requests', function (Blueprint $table) {
            $table->string('request_type')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('period')->nullable();
            $table->text('details')->nullable();
            $table->text('admin_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('partnership_requests', function (Blueprint $table) {
            $table->dropColumn([
                'request_type',
                'quantity',
                'period',
                'details',
                'admin_note',
            ]);
        });
    }
};
