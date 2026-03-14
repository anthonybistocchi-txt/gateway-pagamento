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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained()->onDelete('cascade');
            $table->string('external_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed','refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('quantity')->nullable();
            $table->enum('payment_method', ['card_credit', 'card_debit', 'pix', 'boleto']);
            $table->char('cvv', 4);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
