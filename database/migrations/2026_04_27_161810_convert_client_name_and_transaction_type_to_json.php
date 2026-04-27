<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change column types via Schema builder (cross-database compatible)
        Schema::table('client_logs', function (Blueprint $table) {
            $table->text('client_name')->change();
            $table->text('transaction_type')->change();
        });

        // Wrap existing single-value strings in JSON arrays
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE client_logs SET client_name = json_build_array(client_name)::text WHERE client_name NOT LIKE '[%'");
            DB::statement("UPDATE client_logs SET transaction_type = json_build_array(transaction_type)::text WHERE transaction_type NOT LIKE '[%'");
        } else {
            DB::statement("UPDATE client_logs SET client_name = JSON_ARRAY(client_name) WHERE client_name NOT LIKE '[%'");
            DB::statement("UPDATE client_logs SET transaction_type = JSON_ARRAY(transaction_type) WHERE transaction_type NOT LIKE '[%'");
        }
    }

    public function down(): void
    {
        // Unwrap JSON arrays back to plain strings (first element only)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE client_logs SET client_name = (client_name::jsonb)->>'0'");
            DB::statement("UPDATE client_logs SET transaction_type = (transaction_type::jsonb)->>'0'");
        } else {
            DB::statement("UPDATE client_logs SET client_name = JSON_UNQUOTE(JSON_EXTRACT(client_name, '$[0]'))");
            DB::statement("UPDATE client_logs SET transaction_type = JSON_UNQUOTE(JSON_EXTRACT(transaction_type, '$[0]'))");
        }

        Schema::table('client_logs', function (Blueprint $table) {
            $table->string('client_name')->change();
            $table->string('transaction_type')->change();
        });
    }
};
