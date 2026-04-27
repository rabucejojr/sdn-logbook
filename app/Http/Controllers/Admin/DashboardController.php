<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Allowed sort columns (whitelist to prevent SQL injection)
    private const SORT_COLUMNS = ['date_visited', 'client_name', 'firm_name', 'address', 'attended_by'];

    /**
     * Show the admin dashboard: analytics summary + filterable data table.
     */
    public function index(Request $request)
    {
        // -------------------------------------------------------------------------
        // Pending count (for notification card)
        // -------------------------------------------------------------------------
        $pendingCount = ClientLog::pending()->count();

        // -------------------------------------------------------------------------
        // Analytics: Visitor Counts (approved records only)
        // -------------------------------------------------------------------------
        $totalVisitors = ClientLog::approved()->count();

        $todayVisitors = ClientLog::approved()->whereDate('date_visited', today())->count();

        $weekVisitors = ClientLog::approved()->whereBetween('date_visited', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        $monthVisitors = ClientLog::approved()
            ->whereMonth('date_visited', now()->month)
            ->whereYear('date_visited', now()->year)
            ->count();

        // -------------------------------------------------------------------------
        // Analytics: Chart Data (approved records only)
        // -------------------------------------------------------------------------

        // Transaction type distribution — each record may have multiple types,
        // so we count occurrences of each value across all JSON arrays.
        $transactionDistribution = [];
        ClientLog::approved()->select('transaction_type')->get()->each(function ($log) use (&$transactionDistribution) {
            foreach ((array) $log->transaction_type as $type) {
                $transactionDistribution[$type] = ($transactionDistribution[$type] ?? 0) + 1;
            }
        });

        // Gender distribution (for pie chart)
        $genderDistribution = ClientLog::approved()
            ->select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Daily visitor counts for the last 30 days (for line/bar chart)
        $visitorsOverTime = ClientLog::approved()
            ->select(
                DB::raw('DATE(date_visited) as visit_date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('date_visited', '>=', now()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(date_visited)'))
            ->orderBy('visit_date')
            ->get()
            ->pluck('count', 'visit_date')
            ->toArray();

        // Top 10 municipalities/cities by visit count (for bar chart)
        $topMunicipalities = ClientLog::approved()
            ->select('address', DB::raw('COUNT(*) as count'))
            ->groupBy('address')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'address')
            ->toArray();

        // -------------------------------------------------------------------------
        // Data Table: Search, Filter, Sort, Paginate
        // -------------------------------------------------------------------------
        $search          = $request->input('search');
        $dateFrom        = $request->input('date_from');
        $dateTo          = $request->input('date_to');
        $genderFilter    = $request->input('gender');
        $transactionFilter = $request->input('transaction_type');
        $sortBy          = $request->input('sort_by', 'date_visited');
        $sortDir         = $request->input('sort_dir', 'desc');

        // Sanitize sort inputs
        if (! in_array($sortBy, self::SORT_COLUMNS)) {
            $sortBy = 'date_visited';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $logs = ClientLog::approved()
            ->search($search)
            ->dateRange($dateFrom, $dateTo)
            ->filterGender($genderFilter)
            ->filterTransaction($transactionFilter)
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        $transactionTypes = ['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others'];
        $genders          = ['Male', 'Female', 'Prefer not to say'];

        return view('admin.dashboard', compact(
            // Pending notification
            'pendingCount',
            // Analytics
            'totalVisitors', 'todayVisitors', 'weekVisitors', 'monthVisitors',
            'transactionDistribution', 'genderDistribution',
            'visitorsOverTime', 'topMunicipalities',
            // Table + Filters
            'logs', 'transactionTypes', 'genders',
            'search', 'dateFrom', 'dateTo',
            'genderFilter', 'transactionFilter',
            'sortBy', 'sortDir'
        ));
    }
}
