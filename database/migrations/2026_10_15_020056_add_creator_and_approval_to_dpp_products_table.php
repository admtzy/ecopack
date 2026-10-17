
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dpp_products', function (Blueprint $table) {
            if (!Schema::hasColumn('dpp_products', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }

            if (!Schema::hasColumn('dpp_products', 'approval_status')) {
                $table->string('approval_status')
                    ->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dpp_products', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('dpp_products', 'created_by')) {
                $columns[] = 'created_by';
            }

            if (Schema::hasColumn('dpp_products', 'approval_status')) {
                $columns[] = 'approval_status';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
