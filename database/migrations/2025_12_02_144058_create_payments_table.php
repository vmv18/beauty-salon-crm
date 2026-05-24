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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null')->comment('Пов\'язаний запис (може бути null для окремих платежів)');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->comment('Сума платежу');
            $table->enum('payment_method', ['cash', 'card', 'online'])->default('cash')->comment('Спосіб оплати');
            $table->date('payment_date')->comment('Дата платежу');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->comment('Статус платежу');
            $table->text('notes')->nullable()->comment('Примітки');
            $table->timestamps();
            
            // Індекси для швидкого пошуку
            $table->index('appointment_id');
            $table->index('client_id');
            $table->index('payment_date');
            $table->index('status');
            $table->index('payment_method');
            // Композитний індекс для звітів
            $table->index(['client_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
