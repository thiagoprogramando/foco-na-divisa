<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckIp {

    public function handle(Request $request, Closure $next): Response {

        if (Auth::check()) {

            $exists = DB::table('sessions')->where('id', Session::getId())->where('user_id', Auth::id())->exists();
            if (! $exists) {
                Auth::logout();
                return redirect()->route('login')->with('infor', 'Sua sessão foi encerrada porque você entrou em outro dispositivo!');
            }
        }

        return $next($request);
    }
}
