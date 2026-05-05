<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\KomentarFlag;
 
class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}
 
    public function index()
    {
        $summary     = $this->dashboardService->getAdminSummary();
        $kolamStatus = $this->dashboardService->getKolamStatusList();
        $flagTerbaru = KomentarFlag::with('user')->where('status', 'menunggu')->latest()->take(5)->get();
 
        return view('admin.dashboard', compact('summary', 'kolamStatus', 'flagTerbaru'));
    }
}