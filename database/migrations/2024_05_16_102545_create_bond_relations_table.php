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
        Schema::create('bond_relations', function (Blueprint $table) {
            $table->id();
            $table->integer('value');
            $table->foreignId('bond_id')->constrained('bonds');
            $table->foreignId('salse_bill_id')->nullable()->constrained('salse_bills');
            $table->foreignId('buy_bill_id')->nullable()->constrained('buy_bills');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bond_relations');
    }
};
