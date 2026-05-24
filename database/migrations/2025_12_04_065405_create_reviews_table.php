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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('Рейтинг від 1 до 5');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false)->comment('Чи схвалено модератором');
            $table->timestamps();

            // Індекси для швидкого пошуку
            $table->index('appointment_id');
            $table->index('client_id');
            $table->index('employee_id');
            $table->index('service_id');
            $table->index('is_approved');
            $table->index('rating');

            // Унікальний індекс: один клієнт може залишити один відгук на один запис
            $table->unique(['appointment_id', 'client_id'], 'unique_appointment_client_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
