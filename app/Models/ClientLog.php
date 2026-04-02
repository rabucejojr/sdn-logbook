<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientLog extends Model
{
    protected $fillable = [
        'date_visited',
        'firm_name',
        'client_name',
        'gender',
        'transaction_type',
        'transaction_other_details',
        'address',
        'contact_number',
    ];

    protected $casts = [
        'date_visited' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Returns a display-friendly transaction label.
     * If type is "Others", appends the free-text details.
     */
    public function getTransactionDisplayAttribute(): string
    {
        if ($this->transaction_type === 'Others' && $this->transaction_other_details) {
            return 'Others: ' . $this->transaction_other_details;
        }

        return $this->transaction_type;
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /**
     * Full-text search across client name, firm name, address, and transaction type.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('client_name', 'like', "%{$search}%")
              ->orWhere('firm_name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('transaction_type', 'like', "%{$search}%");
        });
    }

    /**
     * Filter records within a date range (inclusive).
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('date_visited', '>=', $from);
        }

        if ($to) {
            $query->whereDate('date_visited', '<=', $to);
        }

        return $query;
    }

    /**
     * Filter by gender value.
     */
    public function scopeFilterGender(Builder $query, ?string $gender): Builder
    {
        if (blank($gender)) {
            return $query;
        }

        return $query->where('gender', $gender);
    }

    /**
     * Filter by transaction type value.
     */
    public function scopeFilterTransaction(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('transaction_type', $type);
    }
}
