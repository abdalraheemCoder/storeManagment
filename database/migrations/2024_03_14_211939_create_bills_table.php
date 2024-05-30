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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->double('price');
            $table->double('quantity');
            $table->date('date')->default(now());
            $table->double('discount')->nullable();
            $table->enum('typeOfbill',['buy' , 'sale']);
            $table->enum('typeOfpay',['def' , 'cash']);
            $table->longText('note')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
