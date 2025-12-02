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
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        // আপনার ফরেন কি গুলো এখানে থাকবে
        $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
        $table->integer('quantity')->default(0);

        $table->timestamps(); // <--- এই লাইনটি মিসিং ছিল, এটি অবশ্যই যোগ করুন
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
