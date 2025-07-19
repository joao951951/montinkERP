<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('discount_applied', 10, 2);
            $table->decimal('order_subtotal', 10, 2);
            $table->decimal('order_total', 10, 2);
            $table->timestamps();
            
            $table->unique(['coupon_id', 'order_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupon_usages');
    }
};
