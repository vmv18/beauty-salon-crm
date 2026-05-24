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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00)->comment('Рейтинг від 0.00 до 5.00');
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->timestamps();
            
            // Індекси для швидкого пошуку
            $table->index('user_id');
            $table->index('status');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
