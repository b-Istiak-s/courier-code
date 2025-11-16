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
        Schema::create('booking_products', function (Blueprint $table) {
            $table->id();

            // Foreign key to bookings table
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');

            // Foreign key to products table
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // Additional columns
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('description_price', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_products');
    }
};
