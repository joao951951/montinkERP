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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('variation_id')->nullable()->constrained('variations')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();
            
            $table->unique(['product_id', 'variation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};
