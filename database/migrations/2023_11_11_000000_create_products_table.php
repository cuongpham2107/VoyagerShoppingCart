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
            $table->text('description');
            $table->longText('content');
            $table->longText('images');
            $table->text('featured_image');
            $table->longText('product_attributes');
            $table->longText('product_options');
            $table->string('status');
            $table->string('is_featured');
            $table->string('categories');
            $table->integer('brand_id');
            $table->string('product_collections');
            $table->string('product_labels');
            $table->string('product_taxes');
            $table->string('product_tags');
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
