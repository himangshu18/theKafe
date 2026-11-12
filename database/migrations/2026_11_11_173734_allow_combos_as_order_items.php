<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->nullable()->change();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('order_items')->whereNull('menu_item_id')->exists()) {
            throw new RuntimeException('Cannot roll back combo order items while combo order lines exist.');
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->nullable(false)->change();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }
};
