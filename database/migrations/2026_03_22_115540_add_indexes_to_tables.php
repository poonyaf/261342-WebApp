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
        $table->index('user_id');
        $table->index('status');
        $table->index('order_date');
        $table->index('payment_status');
    });

    Schema::table('order_items', function (Blueprint $table) {
        $table->index('order_id');
        $table->index('product_id');
    });

    Schema::table('payments', function (Blueprint $table) {
        $table->index('order_id');
        $table->index('status');
    });

    Schema::table('cart_items', function (Blueprint $table) {
        $table->index('cart_id');
        $table->index('product_id');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropIndex(['user_id']);
        $table->dropIndex(['status']);
        $table->dropIndex(['order_date']);
        $table->dropIndex(['payment_status']);
    });

    Schema::table('order_items', function (Blueprint $table) {
        $table->dropIndex(['order_id']);
        $table->dropIndex(['product_id']);
    });

    Schema::table('payments', function (Blueprint $table) {
        $table->dropIndex(['order_id']);
        $table->dropIndex(['status']);
    });

    Schema::table('cart_items', function (Blueprint $table) {
        $table->dropIndex(['cart_id']);
        $table->dropIndex(['product_id']);
    });
}
};
