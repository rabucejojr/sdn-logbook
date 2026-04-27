<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_logs', function (Blueprint $table) {
            $table->string('status', 10)->default('pending')->after('remarks')->index();
        });

        // All existing records are real, verified data — approve them immediately
        DB::statement("UPDATE client_logs SET status = 'approved'");
    }

    public function down(): void
    {
        Schema::table('client_logs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
