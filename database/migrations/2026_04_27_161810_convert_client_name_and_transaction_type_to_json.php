<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Wrap existing single-value strings in JSON arrays so the array cast
        // decodes them cleanly. Done before changing the column type so MySQL
        // still sees the old TEXT-compatible type during the UPDATE.
        DB::statement("ALTER TABLE client_logs MODIFY COLUMN client_name TEXT NOT NULL");
        DB::statement("UPDATE client_logs SET client_name = CONCAT('[\"', REPLACE(REPLACE(client_name, '\\\\', '\\\\\\\\'), '\"', '\\\\\"'), '\"]') WHERE client_name NOT LIKE '[%'");

        // transaction_type was an ENUM — convert to TEXT first, then wrap values.
        DB::statement("ALTER TABLE client_logs MODIFY COLUMN transaction_type TEXT NOT NULL");
        DB::statement("UPDATE client_logs SET transaction_type = CONCAT('[\"', REPLACE(REPLACE(transaction_type, '\\\\', '\\\\\\\\'), '\"', '\\\\\"'), '\"]') WHERE transaction_type NOT LIKE '[%'");
    }

    public function down(): void
    {
        // Unwrap JSON arrays back to plain strings (first element only).
        DB::statement("UPDATE client_logs SET client_name = JSON_UNQUOTE(JSON_EXTRACT(client_name, '$[0]'))");
        DB::statement("ALTER TABLE client_logs MODIFY COLUMN client_name VARCHAR(255) NOT NULL");

        DB::statement("UPDATE client_logs SET transaction_type = JSON_UNQUOTE(JSON_EXTRACT(transaction_type, '$[0]'))");
        DB::statement(
            "ALTER TABLE client_logs MODIFY COLUMN transaction_type " .
            "ENUM('SETUP','GIA','CEST','Scholarship','S&T Referrals','Others') NOT NULL"
        );
    }
};
