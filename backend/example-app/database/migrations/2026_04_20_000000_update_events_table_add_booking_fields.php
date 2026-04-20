<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('psychologist_id')->nullable()->constrained('psychologists')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->default('konsultacija');
            $table->text('client_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['psychologist_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn(['psychologist_id', 'client_id', 'event_type', 'client_note']);
        });
    }
};
