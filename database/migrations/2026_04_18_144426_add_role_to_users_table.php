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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'employer', 'jobseeker'])->default('jobseeker')->after('email');
            $table->string('company_name')->nullable()->after('role');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->text('company_description')->nullable()->after('company_logo');
            $table->string('phone')->nullable()->after('company_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
