<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function testMail(Request $request)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $to = $data['email'] ?? $request->user()->email;

        try {
            Mail::raw('Test invio email da Antwheels.', function ($message) use ($to) {
                $message->to($to)->subject('Test email Antwheels');
            });
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Invio email fallito',
                'error' => $exception->getMessage(),
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'from' => config('mail.from.address'),
            ], 500);
        }

        return response()->json([
            'message' => 'Email di test inviata',
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'from' => config('mail.from.address'),
            'to' => $to,
        ]);
    }
}
