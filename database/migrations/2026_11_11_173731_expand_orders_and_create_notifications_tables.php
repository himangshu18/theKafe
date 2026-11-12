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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_ref', 24)->nullable()->unique();
            $table->string('source', 20)->default('website');
            $table->string('order_type', 20)->default('pickup');
            $table->string('payment_status', 20)->default('unpaid');
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->dateTime('received_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('estimated_ready_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->index(['status', 'created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_name')->nullable();
            $table->foreignId('menu_variation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('combo_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('variation_name')->nullable();
            $table->json('customizations')->nullable();
            $table->string('combo_name')->nullable();
            $table->json('combo_selections')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('old_status', 32)->nullable();
            $table->string('new_status', 32);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['order_id', 'created_at']);
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 64);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('order_status_histories');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_variation_id']);
            $table->dropForeign(['combo_id']);
            $table->dropForeign(['offer_id']);
            $table->dropColumn([
                'item_name',
                'menu_variation_id',
                'combo_id',
                'offer_id',
                'variation_name',
                'customizations',
                'combo_name',
                'combo_selections',
                'discount_amount',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn([
                'public_ref',
                'source',
                'order_type',
                'payment_status',
                'idempotency_key',
                'discount_amount',
                'received_at',
                'accepted_at',
                'estimated_ready_at',
                'cancellation_reason',
            ]);
        });
    }
};
