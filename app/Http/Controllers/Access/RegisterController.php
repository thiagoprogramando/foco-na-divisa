<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller {
    
    public function index($plan = null) {

        return view('register', [
            'plan' => $plan
        ]);
    }

    public function store(Request $request) {

        $cpfcnpj = preg_replace('/\D/', '', $request->input('cpfcnpj'));
        $request->merge(['cpfcnpj' => $cpfcnpj]);

        $request->validate([
            'name'      => 'required|string|max:255',
            'cpfcnpj'   => 'required|string|max:20|unique:users,cpfcnpj',
            'email'     => 'required|string|email|max:255|unique:users,email',
        ], [
            'name.required'      => 'O nome é obrigatório!',
            'name.max'           => 'O nome não pode ter mais que 255 caracteres!',
            'cpfcnpj.required'   => 'O CPF/CNPJ é obrigatório!',
            'cpfcnpj.max'        => 'O CPF/CNPJ não pode ter mais que 20 caracteres!',
            'cpfcnpj.unique'     => 'Este CPF/CNPJ já está cadastrado!',
            'email.required'     => 'O e-mail é obrigatório!',
            'email.email'        => 'Informe um e-mail válido!',
            'email.max'          => 'O e-mail não pode ter mais que 255 caracteres!',
            'email.unique'       => 'Este e-mail já está cadastrado!',
        ]);

        $user           = new User();
        $user->uuid     = Str::uuid();
        $user->name     = $request->name;
        $user->cpfcnpj = preg_replace('/\D/', '', $request->cpfcnpj);
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        if ($user->save()) {
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                return redirect()->route('app');
            } else {
                return redirect()->route('login')->with('success', 'Bem-vindo(a)! Faça Login para acessar o sistema!');
            }
        }

        return redirect()->back()->with('error', 'Erro ao cadastrar-se, verifique os dados e tente novamente!');
    }
}
