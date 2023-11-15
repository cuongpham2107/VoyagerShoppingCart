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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('images')->nullable();
            $table->text('featured_image')->nullable();
            $table->text('sku')->nullable();
            $table->integer('price')->nullable();
            $table->integer('price_sale')->nullable();
            $table->text('barcode')->nullable();
            $table->text('stock_status')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('length')->nullable();
            $table->integer('wide')->nullable();
            $table->integer('height')->nullable();
            $table->longText('product_attributes')->nullable();
            $table->longText('product_options')->nullable();
            $table->string('status')->nullable();
            $table->string('is_featured')->nullable();
            $table->string('categories')->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('product_collections')->nullable();
            $table->string('product_labels')->nullable();
            $table->string('product_taxes')->nullable();
            $table->string('product_tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
