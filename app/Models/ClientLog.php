<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClientLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_visited',
        'firm_name',
        'client_name',
        'gender',
        'transaction_type',
        'transaction_other_details',
        'address',
        'contact_number',
        'email',
        'attended_by',
        'remarks',
        'status',
    ];

    protected $casts = [
        'date_visited'    => 'datetime',
        'client_name'     => 'array',
        'transaction_type' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Comma-joined list of all client names (single or multiple).
     */
    public function getClientNameDisplayAttribute(): string
    {
        return implode(', ', (array) $this->client_name);
    }

    /**
     * Comma-joined transaction types; "Others" entry appends the free-text detail.
     */
    public function getTransactionDisplayAttribute(): string
    {
        $parts = array_map(function (string $type): string {
            if ($type === 'Others' && $this->transaction_other_details) {
                return 'Others: ' . $this->transaction_other_details;
            }
            return $type;
        }, (array) $this->transaction_type);

        return implode(', ', $parts);
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
              ->orWhere('transaction_type', 'like', "%{$search}%")
              ->orWhere('attended_by', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
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
     * Filter by a single transaction type value within the JSON array.
     */
    public function scopeFilterTransaction(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->whereJsonContains('transaction_type', $type);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}
