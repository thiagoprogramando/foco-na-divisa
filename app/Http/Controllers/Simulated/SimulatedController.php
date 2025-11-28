<?php

namespace App\Http\Controllers\Simulated;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Models\Invoice;
use App\Models\Simulated;
use App\Models\SimulatedQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimulatedController extends Controller {
    
    public function index (Request $request) {

        $query = Simulated::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if (Auth::user()->role != 'admin') {
            $query->where('status', 'active');
        }

        return view('app.Simulated.index', [
            'simulateds' => $query->paginate(10),
        ]);
    }

    public function show ($uuid) {

        $simulated = Simulated::where('uuid', $uuid)->first();
        if (!$simulated) {
            return redirect()->back()->with('infor', 'Simulado não encontrado!');
        }

        if ($simulated->simulatedAnswers()->where('user_id', Auth::user()->id)->where('answer_result', 0)->count() > 0 && $simulated->date_end > now()) {
            return redirect()->route('answer-simulated', ['uuid' => $simulated->uuid]);
        }

        $successCount = SimulatedQuestion::where('simulated_id', $simulated->id)
            ->where('user_id', Auth::user()->id)->where('answer_result', 1)
            ->count();

        $errorCount = SimulatedQuestion::where('simulated_id', $simulated->id)
            ->where('user_id', Auth::user()->id)->where('answer_result', 2)
            ->count();

        $total = $successCount + $errorCount;

        $percentSuccess = $total > 0 ? round(($successCount / $total) * 100, 2) : 0;
        $percentError   = $total > 0 ? round(($errorCount / $total) * 100, 2) : 0;

        $charts = [
            'general' => [
                'success'         => $successCount,
                'error'           => $errorCount,
                'percent_success' => $percentSuccess,
                'percent_error'   => $percentError,
            ],
        ];

        $ranking = SimulatedQuestion::select(
                'user_id',
                DB::raw("SUM(CASE WHEN answer_result = 1 THEN 1 ELSE 0 END) as total_points"),
                DB::raw("SUM(CASE WHEN answer_result <> 0 THEN 1 ELSE 0 END) as total_answered")
                )->where('simulated_id', $simulated->id)->groupBy('user_id')->orderByDesc('total_points')
                ->with(['user:id,name,address_state'])->get()->values()
                ->map(function ($item, $index) {
                    $item->position = $index + 1;
                    return $item;
                });

        return view('app.Simulated.show', [
            'simulated' => $simulated,
            'charts'    => $charts,
            'ranking'   => $ranking,
        ]);
    }

    public function store (Request $request) {

        $simulated              = new Simulated();
        $simulated->uuid        = Str::uuid();
        $simulated->title       = $request->title;
        $simulated->value       = $this->formatValue($request->value);
        $simulated->description = $request->description;
        $simulated->date_start  = $request->date_start;
        $simulated->date_end    = $request->date_end;
        $simulated->status      = $request->status;

        if ($request->hasFile('cover_image')) {
            $simulated->image = $request->file('cover_image')->store('simulateds', 'public');
        }

        if ($simulated->save()) {
            return redirect()->back()->with('success', 'Simulado criado com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao criar o simulado, verifique os dados e tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $simulated = Simulated::where('uuid', $uuid)->first();
        if (!$simulated) {
            return redirect()->back()->with('error', 'Simulado não encontrado!');
        }

        if ($request->filled('title')) {
            $simulated->title = $request->title;
        }
        if ($request->filled('value')) {
            $simulated->value = $this->formatValue($request->value);
        }
        if ($request->filled('description')) {
            $simulated->description = $request->description;
        }
        if ($request->filled('date_start')) {
            $simulated->date_start = $request->date_start;
        }
        if ($request->filled('date_end')) {
            $simulated->date_end = $request->date_end;
        }
        if ($request->filled('status')) {
            $simulated->status = $request->status;
        }
        if ($request->hasFile('cover_image')) {
            $simulated->image = $request->file('cover_image')->store('simulateds', 'public');
        }
        if ($simulated->save()) {
            return redirect()->back()->with('success', 'Simulado atualizado com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao atualizar o simulado, verifique os dados e tente novamente!');
    }

    public function buy (Request $request, $uuid) {

        $simulated = Simulated::where('uuid', $uuid)->first();
        if (!$simulated) {
            return redirect()->back()->with('error', 'Simulado não encontrado!');
        }

        if ($simulated->hasInvoice(Auth::id(), 1)) {
            return redirect()->route('invoices')->with('infor', 'Você já comprou o Simulado!');   
        }

        Invoice::where('user_id', Auth::id())
            ->where('payment_status', '<>', 1)
            ->where('simulated_id', $simulated->id)
            ->update(['payment_status' => 2]);

        $assasController    = new AssasController();

        $customer = $assasController->createdCustomer(Auth::user()->name, Auth::user()->cpfcnpj, Auth::user()->phone, Auth::user()->email);
        if ($customer === false) {
            return redirect()->back()->with('error', 'Verfique seus dados e tente novamente!');   
        }

        $charge = $assasController->createdCharge($customer, $request->payment_method, $request->payment_installments, $value = $simulated->value, $description = 'Compra do Simulado: ' . $simulated->title, now()->addDays(3), $commissions = null);
        if ($charge === false) {
            return redirect()->back()->with('error', 'Falha ao gerar a cobrança, tente novamente!');   
        }

        $invoice                  = new Invoice();
        $invoice->uuid            = Str::uuid();
        $invoice->user_id         = Auth::user()->id;
        $invoice->simulated_id    = $simulated->id;
        $invoice->payment_status  = 0;
        $invoice->value           = $simulated->value;
        $invoice->due_date        = now()->addDays(3);
        $invoice->payment_splits  = $charge['paymentSplits'] ?? null;
        $invoice->payment_token   = $charge['id'];
        $invoice->payment_url     = $charge['invoiceUrl'];
        $invoice->payment_status  = 0;
        if ($invoice->save()) {
            return redirect($charge['invoiceUrl']);
        } else {
            return redirect()->back()->with('error', 'Falha ao gerar a cobrança, tente novamente!');
        }
    }

    private function formatValue ($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
