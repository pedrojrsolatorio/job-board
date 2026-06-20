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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('match_score')->nullable()->after('status');
            $table->text('ai_summary')->nullable()->after('match_score');
            $table->json('ai_strengths')->nullable()->after('ai_summary');
            $table->json('ai_gaps')->nullable()->after('ai_strengths');
            $table->enum('ai_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')->after('ai_gaps');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_status');
            $table->text('ai_error')->nullable()->after('ai_analyzed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['match_score', 'ai_summary', 'ai_strengths', 'ai_gaps', 'ai_status', 'ai_analyzed_at', 'ai_error']);
        });
    }
};
