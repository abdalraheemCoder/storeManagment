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
            //$table->foreignId('bond_id')->constrained('bonds');
            $table->foreignId('bill_id')->nullable()->constrained('bills');
            $table->foreignId('acc_id')->nullable()->constrained('accounts');
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
