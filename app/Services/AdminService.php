<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_quotes' => Quote::count(),
            'completed_quotes' => Quote::where('status', 'completed')->count(),
            'draft_quotes' => Quote::where('status', 'draft')->count(),
            'exported_quotes' => Quote::where('status', 'exported')->count(),
            'total_revenue' => Quote::where('status', 'completed')
                ->sum('total_amount') ?? 0,
            'average_quote_value' => Quote::where('status', 'completed')
                ->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Get all users with pagination
     */
    public function getAllUsers(int $page = 1, int $perPage = 15)
    {
        return User::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get quotes report with details
     */
    public function getQuotesReport(int $page = 1, int $perPage = 15)
    {
        return Quote::with(['user', 'configuration.wheelCategory', 'configuration.wheelHub'])
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get quote statistics by status
     */
    public function getQuotesByStatus(): array
    {
        return [
            'draft' => Quote::where('status', 'draft')->count(),
            'completed' => Quote::where('status', 'completed')->count(),
            'exported' => Quote::where('status', 'exported')->count(),
        ];
    }

    /**
     * Get most popular wheel categories
     */
    public function getPopularWheelCategories(int $limit = 5): array
    {
        return DB::table('configurations')
            ->join('wheel_categories', 'configurations.wheel_category_id', '=', 'wheel_categories.id')
            ->select('wheel_categories.id', 'wheel_categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('wheel_categories.id', 'wheel_categories.name')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get revenue by month
     */
    public function getRevenueByMonth(int $months = 12): array
    {
        return DB::table('quotes')
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy(DB::raw('DATE_TRUNC(\'month\', created_at)'))
            ->orderBy('month', 'desc')
            ->limit($months)
            ->get()
            ->toArray();
    }
}
