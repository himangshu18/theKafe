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
        if (! Schema::hasColumn('menu_categories', 'description')) {
            Schema::table('menu_categories', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
        if (! Schema::hasColumn('menu_categories', 'image_path')) {
            Schema::table('menu_categories', function (Blueprint $table) {
                $table->string('image_path')->nullable();
            });
        }
        if (! Schema::hasColumn('menu_categories', 'deleted_at')) {
            Schema::table('menu_categories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('menu_items', 'food_type')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->string('food_type', 20)->default('veg');
            });
        }
        if (! Schema::hasColumn('menu_items', 'preparation_minutes')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->unsignedSmallInteger('preparation_minutes')->default(0);
            });
        }
        if (! Schema::hasColumn('menu_items', 'available_from')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dateTime('available_from')->nullable();
            });
        }
        if (! Schema::hasColumn('menu_items', 'available_until')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dateTime('available_until')->nullable();
            });
        }
        if (! Schema::hasColumn('menu_items', 'deleted_at')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (! Schema::hasIndex('menu_items', 'menu_items_food_type_is_available_index')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->index(['food_type', 'is_available']);
            });
        }

        if (! Schema::hasTable('menu_item_images')) {
            Schema::create('menu_item_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
                $table->string('image_path');
                $table->string('alt_text')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_variations')) {
            Schema::create('menu_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->decimal('price', 10, 2);
                $table->boolean('is_available')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('addon_groups')) {
            Schema::create('addon_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedTinyInteger('min_select')->default(0);
                $table->unsignedTinyInteger('max_select')->default(1);
                $table->boolean('is_required')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('addon_options')) {
            Schema::create('addon_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->decimal('price_delta', 10, 2)->default(0);
                $table->boolean('is_available')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_item_addon_group')) {
            Schema::create('menu_item_addon_group', function (Blueprint $table) {
                $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
                $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
                $table->primary(['menu_item_id', 'addon_group_id']);
            });
        }

        if (! Schema::hasTable('menu_item_availability_schedules')) {
            Schema::create('menu_item_availability_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week');
                $table->time('starts_at');
                $table->time('ends_at');
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasIndex('menu_item_availability_schedules', 'menu_item_schedule_unique')) {
            Schema::table('menu_item_availability_schedules', function (Blueprint $table) {
                $table->unique(
                    ['menu_item_id', 'day_of_week', 'starts_at'],
                    'menu_item_schedule_unique'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_availability_schedules');
        Schema::dropIfExists('menu_item_addon_group');
        Schema::dropIfExists('addon_options');
        Schema::dropIfExists('addon_groups');
        Schema::dropIfExists('menu_variations');
        Schema::dropIfExists('menu_item_images');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['food_type', 'is_available']);
            $table->dropSoftDeletes();
            $table->dropColumn(['food_type', 'preparation_minutes', 'available_from', 'available_until']);
        });

        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['description', 'image_path']);
        });
    }
};
