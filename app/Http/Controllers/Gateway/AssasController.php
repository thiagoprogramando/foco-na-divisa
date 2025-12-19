<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Simulated;
use App\Models\SimulatedQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AssasController extends Controller {

    public function createdCustomer ($name, $cpfcnpj, $mobilePhone = null, $email = null) {

        try {
            $client = new Client();
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'accept'       => 'application/json',
                    'access_token' => env('API_TOKEN_ASSAS'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'name'        => $name,
                    'cpfCnpj'     => $cpfcnpj,
                    'mobilePhone' => $mobilePhone,
                    'email'       => $email,
                ],
                'verify' => false
            ];
    
            $response = $client->post(env('API_URL_ASSAS') . 'v3/customers', $options);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);
    
            if ($response->getStatusCode() === 200 && isset($data['id'])) {
                return $data['id'];
            } else {
                Log::error("Erro na criação do cliente: " . json_encode($data));
                return false;
            }
    
        } catch (RequestException $e) {
            Log::error("Erro de requisição na API Assas CreateCustomer: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error("Erro geral na função createCustomer: " . $e->getMessage());
            return false;
        }
    }
    
    public function createdCharge ($customer, $billingType, $installments = null, $value, $description, $dueDate = null, $commissions = null) {
        try {
            $client = new Client();
    
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => env('API_TOKEN_ASSAS'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'customer'          => $customer,
                    'billingType'       => $billingType,
                    'installmentCount'  => $installments ?? 1,
                    'installmentValue'  => number_format(($value / ($installments ?? 1)), 2, '.', ''),
                    'value'             => number_format($value, 2, '.', ''),
                    'dueDate'           => isset($dueDate) ? Carbon::parse($dueDate)->toIso8601String() : now()->addDays(1),
                    'description'       => $description,
                    'isAddressRequired' => false,
                    'split'             => $commissions,
                ],
                'verify' => false
            ];

            if (env('APP_ENV') !== 'local') {
                $options['json']['callback'] =  ['successUrl' => env('APP_URL')];
            }
    
            $response = $client->post(env('API_URL_ASSAS') . 'v3/payments', $options);
            $body = (string) $response->getBody();
    
            if ($response->getStatusCode() === 200) {
                $data = json_decode($body, true);
                return [
                    'id'            => $data['id'],
                    'invoiceUrl'    => $data['invoiceUrl'],
                    'splits'        => $data['split'] ?? [],
                ];
            } else {
                Log::error('Erro ao Gerar Fatura (Controller AssasController) de '.$customer.': ' . $body);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Erro ao Gerar Fatura (Controller AssasController) de '.$customer.': ' . $e->getMessage());
            return false;
        }
    }

    public function webhook(Request $request) {

        $jsonData   = $request->json()->all();
        $token      = $jsonData['payment']['id'];

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {
            
            $invoice = Invoice::where('payment_token', $token)->whereIn('payment_status', [0, 2])->first();
            if ($invoice) {

                if ($invoice->simulated_id) {
                    $this->generateSimulatedForUser($invoice->user, $invoice->simulated);
                }

                $invoice->payment_status = 1;
                if ($invoice->save()) {
                    
                    Invoice::where('user_id', $invoice->user_id)->where('id', '!=', $invoice->id)->whereIn('payment_status', [0, 2])->delete();
                    return response()->json(['message' => 'Fatura Aprovada!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Aprovar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhuma Fatura para o TOKEN!'], 200);
        };

        if ($jsonData['event'] === 'PAYMENT_OVERDUE') {

            $invoice = Invoice::where('payment_token', $token)->whereIn('payment_status', [0, 2])->first();
            if ($invoice) {

                $invoice->payment_status = 2;
                if ($invoice->save()) {
                    return response()->json(['message' => 'Fatura Cancelada por vencimento!'], 200);
                } else {
                    return response()->json(['message' => 'Falha o tentar Cancelar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhuma Fatura para o TOKEN!'], 200);
        };

        if ($jsonData['event'] === 'PAYMENT_DELETED') {
            
            $invoice = Invoice::where('payment_token', $token)->first();
            if ($invoice && $invoice->delete()) {
                return response()->json(['message' => 'Fatura deletada via Assas e espelhada no app!'], 200);
            }

            return response()->json(['message' => 'Nenhuma Fatura para o TOKEN!'], 200);
        };

        if ($jsonData['event'] === 'PAYMENT_RESTORED') {

            $invoice = Invoice::withTrashed()->where('payment_token', $token)->first();
            if ($invoice) {
                if ($invoice->trashed()) {
                    $invoice->restore();
                    return response()->json(['message' => 'Fatura restaurada via Assas e espelhada no app!'], 200);
                }

                return response()->json(['message' => 'Fatura já está ativa, nenhuma ação necessária!'], 200);
            }

            return response()->json(['message' => 'Nenhuma Fatura para o TOKEN!'], 200);
        };
        
        return response()->json(['message' => 'Nenhum Evento disponível!'], 200);
    }

    private function generateSimulatedForUser($user, $simulated) {

        $questions = $simulated->questions;
        $position  = 1;

        if (count($questions) <= 0) {
            return false;
        }

        foreach ($questions->sortBy('simulated_question_position') as $q) {
            SimulatedQuestion::create([
                'user_id'           => $user->id,
                'simulated_id'      => $simulated->id,
                'question_id'       => $q->id,
                'question_position' => $position++,
                'answer_result'     => 0,
            ]);
        }

        return true;
    }

}
