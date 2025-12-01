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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
        $table->string('invoice_no')->unique();

        // বিলের হিসাব
        $table->decimal('subtotal', 12, 2);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('tax', 12, 2)->default(0);
        $table->decimal('total_amount', 12, 2); 

        // পেমেন্ট ইনফো
        $table->decimal('paid_amount', 12, 2);
        $table->decimal('due_amount', 12, 2)->default(0);
        $table->string('payment_method')->default('Cash'); // Cash, Card, Bkash

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
