<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\driver;
use App\Models\customer;
use App\Models\material;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salse_bills', function (Blueprint $table) {
            $table->id();
            $table->string('salseBill_details')->nullable();
            $table->double('price');
            $table->double('quantity');
            $table->date('date');
            $table->double('discount')->nullable();
            $table->enum('type',['buy' , 'sale']);
            $table->longText('note')->nullable();
            //$table->foreignId('customer_id')->constrained('customers');
            //$table->foreignId('driver_id')->constrained('drivers');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salse_bills');
    }
};
