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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('photo_before')->nullable()->after('cancellation_reason')->comment('Фото до послуги');
            $table->string('photo_after')->nullable()->after('photo_before')->comment('Фото після послуги');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['photo_before', 'photo_after']);
        });
    }
};
