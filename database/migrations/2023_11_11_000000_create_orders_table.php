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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->text('note')->nullable();
            $table->integer('sub_amount')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->integer('promotion_amount')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('shipping_amount')->nullable();
            $table->integer('total_amount')->nullable();
            $table->text('payment_option')->nullable();
            $table->text('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
