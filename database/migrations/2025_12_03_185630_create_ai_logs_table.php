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
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->string('model')->default('deepseek-chat'); // AI model used
            $table->text('prompt'); // Input sent to AI
            $table->json('response'); // Raw AI response
            $table->decimal('confidence', 3, 2)->nullable(); // Confidence score 0.00-1.00
            $table->integer('processing_time_ms')->nullable(); // API response time
            $table->string('status')->default('success'); // success, error, timeout
            $table->text('error_message')->nullable(); // Error details if failed
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
