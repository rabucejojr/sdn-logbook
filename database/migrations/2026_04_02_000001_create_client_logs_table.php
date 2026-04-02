<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the client_logs table to store all visiting client records.
     */
    public function up(): void
    {
        Schema::create('client_logs', function (Blueprint $table) {
            $table->id();

            // Date/time the client visited — set server-side on form submission
            $table->timestamp('date_visited');

            $table->string('firm_name');
            $table->string('client_name');

            $table->enum('gender', ['Male', 'Female', 'Prefer not to say']);

            $table->enum('transaction_type', [
                'SETUP',
                'GIA',
                'CEST',
                'Scholarship',
                'S&T Referrals',
                'Others',
            ]);

            // Only populated when transaction_type = 'Others'
            $table->text('transaction_other_details')->nullable();

            // Format: Municipality/City (e.g., "Surigao City")
            $table->string('address');

            // Philippine mobile number: 09XXXXXXXXX or +639XXXXXXXXX
            $table->string('contact_number', 20);

            $table->timestamps();

            // Indexes for common query patterns
            $table->index('date_visited');
            $table->index('transaction_type');
            $table->index('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_logs');
    }
};
