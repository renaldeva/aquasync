<?php

namespace App\Http\Controllers\Owner;
 
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\KomentarFlag;
 
class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}
 
    public function index()
    {
        $summary     = $this->dashboardService->getOwnerSummary();
        $kolamStatus = $this->dashboardService->getKolamStatusList();
        $flagSaya    = KomentarFlag::with('penjawab')
            ->where('user_id', auth()->id())
            ->latest()->take(3)->get();
 
        return view('owner.dashboard', compact('summary', 'kolamStatus', 'flagSaya'));
    }
}