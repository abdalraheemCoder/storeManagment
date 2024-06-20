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
        Schema::create('bills_details', function (Blueprint $table) {
            $table->id();
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(1);
            $table->double('discount')->default(0);
            // $table->double('discount % ')->default(0);
            // $table->double('totalPrice')->default(0);
            $table->longText('note')->nullable();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('material_id')->constrained('materials');
            $table->foreignId('bill_id')->constrained('bills');
            $table->enum('type', ['buy', 'sale','re_sale','re_buy']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills_details');
    }
};
