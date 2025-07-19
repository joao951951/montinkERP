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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            // $table->string('sku')->nullable();
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['product_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
};
