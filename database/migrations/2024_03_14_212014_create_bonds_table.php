<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bonds', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('account_id')->constrained('accounts');
            $table->double('value');
            $table->enum('type',['receipt' , 'payment']);
            $table->longText('note')->nullable();
            //$table->foreignId('bondRel_id')->constrained('bond_relations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonds');
    }
};
