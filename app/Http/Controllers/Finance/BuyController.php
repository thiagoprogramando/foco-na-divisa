<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;

use App\Models\Invoice;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BuyController extends Controller {
    
    public function store(Request $request, $product) {
        
        $product = Product::where('uuid', $product)->first();
        if (!$product || $product->status !== 1) {
            return redirect()->route('plans')->with('error', 'Produto indisponível!');   
        }

        if ($product->hasInvoice(Auth::id(), 0)) {
            return redirect()->route('invoices')->with('infor', 'Você já assinou o produto!');   
        }

        $assasController    = new AssasController();
        $dueDate            = $product->calculateDueDate();

        $customer = $assasController->createdCustomer(Auth::user()->name, Auth::user()->cpfcnpj, Auth::user()->phone, Auth::user()->email);
        if ($customer === false) {
            return redirect()->route('plans')->with('error', 'Verfique seus dados e tente novamente!');   
        }

        $charge = $assasController->createdCharge($customer, $request->payment_method, $request->payment_installments, $value = $product->value, $description = 'Compra do Produto: ' . $product->name, now()->addDays(1), $commissions = null);
        if ($charge === false) {
            return redirect()->route('plans')->with('error', 'Erro ao gerar a cobrança, tente novamente!');   
        }

        Invoice::where('user_id', Auth::id())
            ->where('payment_status', 0)
            ->whereHas('product', fn($q) => $q->where('type', 'plan'))
            ->update(['payment_status' => 2]);

        $invoice                  = new Invoice();
        $invoice->uuid            = Str::uuid();
        $invoice->user_id         = Auth::user()->id;
        $invoice->product_id      = $product->id;
        $invoice->payment_status  = 0;
        $invoice->value           = $product->value;
        $invoice->due_date        = $dueDate;
        $invoice->payment_splits  = $charge['paymentSplits'] ?? null;
        $invoice->payment_token   = $charge['id'];
        $invoice->payment_url     = $charge['invoiceUrl'];
        $invoice->payment_status  = 0;
        if ($invoice->save()) {

            $product->increment('views');
            $product->save();
            return redirect($charge['invoiceUrl']);
        } else {
            return redirect()->route('plans')->with('error', 'Erro ao gerar a cobrança, tente novamente!');
        }
    }

}
