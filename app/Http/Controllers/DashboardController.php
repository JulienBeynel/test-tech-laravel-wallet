<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request)
    {
        $transactions = $request->user()->wallet ? $request->user()->wallet->transactions()->with('transfer')->orderByDesc('id')->get(): [];
        $balance = $request->user()->wallet ? $request->user()->wallet->balance : 0;

        return view('dashboard', compact('transactions', 'balance'));
    }
}
