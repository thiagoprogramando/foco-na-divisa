<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;

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
    
    public function createdCharge ($customer, $billingType, $value, $description, $dueDate = null, $commissions = null) {
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
                    'value'             => number_format($value, 2, '.', ''),
                    'dueDate'           => isset($dueDate) ? Carbon::parse($dueDate)->toIso8601String() : now()->addDays(7),
                    'description'       => $description,
                    'isAddressRequired' => false,
                    'split'             => $commissions,
                    'callback'          => ['successUrl'    => env('APP_URL') . '/app']
                ],
                'verify' => false
            ];
    
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
        
        //Recebe os dados do webhook
        //Validar invoice
        //Se invoice for de um plano elimina às demais invoices de outros planos
        
    }
}
