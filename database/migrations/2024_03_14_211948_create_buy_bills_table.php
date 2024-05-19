<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\supplier;
use App\Models\material;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buy_bills', function (Blueprint $table) {
            $table->id();
            $table->string('buyBill_details')->nullable();
            $table->double('price');
            $table->double('quantity');
            $table->date('date');
            $table->double('discount')->nullable();
            $table->boolean('type');
            $table->longText('note')->nullable();
            //$table->foreignId('suppl8ier_id')->constrained('suppliers');
            $table->timestamps();


        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_bills');
    }
};
