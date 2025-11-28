<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Gateway\AssasController;
use App\Models\Invoice;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckMonthly {
    
    public function handle(Request $request, Closure $next): Response {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        if ($user->role == 'admin') {
            return $next($request);
        }

        $invoice = Invoice::where('user_id', $user->id)->whereHas('product', fn ($q) => $q->where('type', 'plan'))->latest('due_date')->first();
        if (!$invoice) {
            return redirect()->route('plans')->with('infor', 'Escolha um plano para continuar!');
        }

        if ($invoice->payment_status == 1 && $invoice->due_date >= now()->toDateString()) {
            return $next($request);
        }

        $hasUsedTrial = Invoice::where('user_id', $user->id)
            ->whereHas('product', fn($q) => $q->where('type', 'plan'))
            ->whereIn('payment_status', [0, 1, 2])
            ->where('id', '<>', $invoice->id)
            ->exists();

        if (!$hasUsedTrial) {
            if (now()->lessThanOrEqualTo($invoice->created_at->addDays())) {
                return $next($request);
            }
        }

        if ($invoice->payment_status !== 1) {
            return redirect()->route('invoices')->with('infor', 'O Período de teste do seu plano expirou. É hora de pagar sua Fatura!');
        }

        if ($invoice->due_date < now()->toDateString() && $invoice->payment_status == 1) {
            return redirect()->route('plans')->with('infor', 'Sua assinatura expirou. Renove seu plano para continuar!');
        }

        return $next($request);
    }
    
}
