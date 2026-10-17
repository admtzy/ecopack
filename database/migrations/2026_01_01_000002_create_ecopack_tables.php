<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->text('address')->nullable();
            $t->string('contact_name')->nullable();
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('dpp_products', function (Blueprint $t) {
            $t->id();
            $t->string('product_code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->text('materials')->nullable();
            $t->text('production_info')->nullable();
            $t->text('end_of_life')->nullable();
            $t->string('approval_status')->default('pending');
            $t->timestamps();
        });

        Schema::create('qr_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('dpp_product_id')
                ->constrained('dpp_products')
                ->cascadeOnDelete();
            $t->string('token')->unique();
            $t->timestamps();
        });

        Schema::create('scan_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()
                ->constrained()->nullOnDelete();
            $t->foreignId('qr_code_id')
                ->constrained('qr_codes')->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('point_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->integer('points');
            $t->string('type');
            $t->string('description');
            $t->string('reference_type')->nullable();
            $t->unsignedBigInteger('reference_id')->nullable();
            $t->timestamps();
            $t->index(['reference_type', 'reference_id']);
        });

        Schema::create('rewards', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('description')->nullable();
            $t->string('image_path')->nullable();
            $t->unsignedInteger('points_required');
            $t->unsignedInteger('stock')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('redemptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('reward_id')
                ->constrained('rewards')->restrictOnDelete();
            $t->unsignedInteger('quantity')->default(1);
            $t->unsignedInteger('points_spent');

            $t->string('recipient_name', 100)->nullable();
            $t->string('phone', 20)->nullable();
            $t->text('shipping_address')->nullable();

            $t->string('status')->default('pending');
            $t->text('admin_note')->nullable();
            $t->timestamps();
        });

        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('redemption_id')
                ->constrained('redemptions')->cascadeOnDelete();
            $t->string('tracking_number')->nullable();
            $t->string('status')->default('waiting');
            $t->timestamp('shipped_at')->nullable();
            $t->timestamp('delivered_at')->nullable();
            $t->timestamps();
        });

        Schema::create('partnership_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('status')->default('pending');
            $t->text('message')->nullable();
            $t->timestamps();
        });

        Schema::create('product_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('product_name');
            $t->text('description')->nullable();
            $t->string('status')->default('pending');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_requests');
        Schema::dropIfExists('partnership_requests');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('redemptions');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('scan_logs');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('dpp_products');
        Schema::dropIfExists('companies');
    }
};
