<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the two extra columns required by the task as a standalone migration,
     * so the change to the existing `projects` table is fully versioned.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('deadline')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'deadline']);
        });
    }
};
