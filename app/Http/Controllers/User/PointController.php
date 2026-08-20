<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PointService;

class PointController extends Controller
{
    public function index(PointService $pointService)
    {
        $member = auth()->guard('member')->user();

        $transactions = $member->pointTransactions()
            ->with('event')
            ->paginate(15);

        return inertia('User/Points/Index', [
            'points' => $pointService->getBalance($member),
            'transactions' => $transactions,
        ]);
    }
}
