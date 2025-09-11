<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole {

    public function handle(Request $request, Closure $next): Response {
        
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        if ($user->role == 'admin') {
            return $next($request);
        }

        return redirect()->route('app')->with('infor', 'Acesso negado!');
    }
}
