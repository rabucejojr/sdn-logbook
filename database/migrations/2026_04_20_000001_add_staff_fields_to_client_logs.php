<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add staff-only fields to the client_logs table.
     * These are filled by DOST staff before the client submits the form.
     */
    public function up(): void
    {
        Schema::table('client_logs', function (Blueprint $table) {
            // Nullable at DB level so existing rows are not broken; required at app level
            $table->string('attended_by')->nullable()->after('contact_number');
            $table->text('remarks')->nullable()->after('attended_by');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('client_logs', function (Blueprint $table) {
            $table->dropColumn(['attended_by', 'remarks']);
        });
    }
};
