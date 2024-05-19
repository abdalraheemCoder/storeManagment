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
            $table->integer('discount')->default(0);
            $table->longText('note');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('material_id')->constrained('materials');
            $table->foreignId('salse_bill_id')->constrained('salse_bills');
            $table->foreignId('buy_bill_id')->constrained('buy_bills');
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
