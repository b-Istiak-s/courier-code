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
        Schema::create('setup_charges', function (Blueprint $table) {
            $table->id(); // local primary key

            $table->decimal('fulfilment_fee')->nullable(); // external zone id
            $table->decimal('product_charges')->nullable(); // external area id
            $table->decimal('delivery_charges')->nullable(); // external area id
            $table->decimal('cod_fee')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_charges');
    }
};
