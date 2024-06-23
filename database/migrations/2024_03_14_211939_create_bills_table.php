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
            $table->double('price')->nullable();
            $table->double('quantity')->nullable();
            $table->date('date')->default(now());
            $table->double('discount')->nullable();
            $table->enum('typeOfbill',['buy' , 'sale','re_sale','re_buy']);
            $table->enum('typeOfpay',['def' , 'cash']);
            $table->longText('note')->nullable();
            // $table->double('discount % ')->nullable();
            //$table->date('expier_date')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('cascade');
            //$table->foreignId('supplier_id')->nullable()->constrained('suppliers');
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
