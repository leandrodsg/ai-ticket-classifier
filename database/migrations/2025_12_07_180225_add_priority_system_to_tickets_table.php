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
        Schema::table('tickets', function (Blueprint $table) {
            // Add priority system fields after confidence column
            $table->string('priority')->nullable()->after('confidence');
            $table->timestamp('sla_due_at')->nullable()->after('priority');
            $table->string('impact_level')->nullable()->after('sla_due_at');
            $table->string('urgency_level')->nullable()->after('impact_level');
            $table->timestamp('escalated_at')->nullable()->after('urgency_level');

            // Add indexes for performance
            $table->index('priority');
            $table->index('sla_due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropIndex(['sla_due_at']);

            $table->dropColumn([
                'priority',
                'sla_due_at',
                'impact_level',
                'urgency_level',
                'escalated_at'
            ]);
        });
    }
};
