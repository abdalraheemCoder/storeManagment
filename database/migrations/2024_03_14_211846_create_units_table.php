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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name');
            $table->integer('unit_equal');
            $table->integer('Quantity')->default(0);
            $table->integer('Quan_return')->default(0);
            $table->double('unitSalse_price')->default(0);
            $table->double('unitbuy_price')->default(0);;
            //$table->foreignId('unit_mat_id')->constrained('materials');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
