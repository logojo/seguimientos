<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureLlaveAuthenticated
{

    public function handle(Request $request, Closure $next): Response
    {

        
        // 1. El usuario debe estar autenticado en Laravel
        if (!Auth::check()) {
            return redirect()->route('llave.login')
                ->with('error', 'Debes iniciar sesión con LlaveMX.');
        }


        //*Se usara cuando se requiera realizar acciones que tengan que relacionarse con llave mx
        // 2. Token LlaveMX debe existir
        // $token = Session::get('llave_access_token');
        // if (!$token) {
        //     Auth::logout();
        //     return redirect()->route('llave.login')
        //         ->with('error', 'Tu sesión con LlaveMX ha expirado.');
        // }

        return $next($request);
    }
}
