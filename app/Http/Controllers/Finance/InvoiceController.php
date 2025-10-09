<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller {
    
    public function index(Request $request) {

        $user   = Auth::user();
        $query  = Invoice::orderByDesc('created_at');

        $query->when(
            $user->role === 'admin' && $request->filled('user_id') && $request->user_id !== 'Opções Disponíveis',
            fn($q) => $q->where('user_id', $request->user_id)
        )->when(
            $user->role !== 'admin',
            fn($q) => $q->where('user_id', $user->id)
        );

        $query->when(
            $request->filled('payment_status') && $request->payment_status !== 'Opções Disponíveis',
            fn($q) => $q->where('payment_status', $request->payment_status)
        );

        $query->when(
            $request->filled('product_id') && $request->product_id !== 'Opções Disponíveis',
            fn($q) => $q->where('product_id', $request->product_id)
        );

        $query->when(
            $request->filled('start_date'),
            fn($q) => $q->whereDate('created_at', '>=', \Carbon\Carbon::parse($request->start_date))
        );

        $query->when(
            $request->filled('end_date'),
            fn($q) => $q->whereDate('created_at', '<=', \Carbon\Carbon::parse($request->end_date))
        );

        return view('app.Finance.invoices', [
            'invoices' => $query->paginate(10),
            'products' => Product::orderBy('name')->get(),
            'users'    => User::orderBy('name')->get(),
        ]);
    }

}
