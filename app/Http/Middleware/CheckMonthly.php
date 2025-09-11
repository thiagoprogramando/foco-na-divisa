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
            if (now()->lessThanOrEqualTo($invoice->created_at->addDays(7))) {
                return $next($request);
            }
        }

        if ($invoice->payment_status !== 1) {
            return redirect()->to(route('user', ['uuid' => $user->uuid]) . '#invoices')
                ->with('infor', 'O Período de teste do seu plano expirou. É hora de pagar sua Fatura!');
        }

        if ($invoice->due_date < now()->toDateString() && $invoice->payment_status == 1) {

            $assasController = new AssasController();

            $customer = $assasController->createdCustomer($user->name, $user->cpfcnpj, $user->phone, $user->email);
            if ($customer === false) {
                return redirect()->route('plans')->with('error', 'Verfique seus dados e tente novamente!');   
            }

            $product = $invoice->product;
            $dueDate = $product->calculateDueDate();

            $charge = $assasController->createdCharge($customer, 'UNDEFINED', $product->value, 'Compra do Produto: ' . $product->name, $dueDate);
            if ($charge === false) {
                return redirect()->route('plans')->with('error', 'Erro ao gerar a cobrança, tente novamente!');   
            }

            $newInvoice                  = new Invoice();
            $newInvoice->uuid            = Str::uuid();
            $newInvoice->user_id         = $user->id;
            $newInvoice->product_id      = $product->id;
            $newInvoice->payment_status  = 0;
            $newInvoice->value           = $product->value;
            $newInvoice->due_date        = $dueDate;
            $newInvoice->payment_splits  = $charge['paymentSplits'] ?? null;
            $newInvoice->payment_token   = $charge['id'];
            $newInvoice->payment_url     = $charge['invoiceUrl'];

            if ($newInvoice->save()) {
                return redirect($charge['invoiceUrl']);
            }

            return redirect()->route('user', ['uuid' => $user->uuid])
                ->with('infor', 'Sua assinatura precisa ser renovada. Geramos uma nova fatura.');
        }

        return $next($request);
    }
    
}
