<?php

namespace App\Http\Controllers;

use App\Services\AdminService;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * Get dashboard statistics
     */
    public function dashboardStats()
    {
        $stats = $this->adminService->getDashboardStats();
        
        return response()->json([
            'stats' => $stats,
        ]);
    }

    /**
     * Get all users
     */
    public function getUsers()
    {
        $users = $this->adminService->getAllUsers();
        
        return response()->json($users);
    }

    /**
     * Get quotes report
     */
    public function getQuotesReport()
    {
        $report = $this->adminService->getQuotesReport();
        
        return response()->json($report);
    }

    /**
     * Get quotes by status
     */
    public function getQuotesByStatus()
    {
        $stats = $this->adminService->getQuotesByStatus();
        
        return response()->json([
            'status' => $stats,
        ]);
    }

    /**
     * Get popular wheel categories
     */
    public function getPopularWheelCategories()
    {
        $models = $this->adminService->getPopularWheelCategories();
        
        return response()->json([
            'models' => $models,
        ]);
    }

    /**
     * Get revenue by month
     */
    public function getRevenueByMonth()
    {
        $revenue = $this->adminService->getRevenueByMonth();
        
        return response()->json([
            'revenue' => $revenue,
        ]);
    }
}
