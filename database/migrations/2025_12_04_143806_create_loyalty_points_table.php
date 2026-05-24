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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->integer('points')->default(0)->comment('Кількість балів (може бути від\'ємним при використанні)');
            $table->enum('type', ['earned', 'spent', 'expired'])->default('earned')->comment('Тип операції');
            $table->string('description')->nullable()->comment('Опис операції');
            $table->integer('balance_after')->default(0)->comment('Баланс після операції');
            $table->timestamps();

            // Індекси
            $table->index('client_id');
            $table->index('appointment_id');
            $table->index('type');
            $table->index('created_at');
        });

        // Додати поля до таблиці clients для швидкого доступу
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('status')->comment('Поточний баланс балів');
            $table->enum('loyalty_level', ['bronze', 'silver', 'gold'])->default('bronze')->after('loyalty_points')->comment('Рівень лояльності');
            $table->integer('total_loyalty_points_earned')->default(0)->after('loyalty_level')->comment('Всього зароблено балів');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'loyalty_level', 'total_loyalty_points_earned']);
        });

        Schema::dropIfExists('loyalty_points');
    }
};
