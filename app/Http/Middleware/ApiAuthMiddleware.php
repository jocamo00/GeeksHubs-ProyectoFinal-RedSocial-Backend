<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Comprobar si el usuario esta identificado
        // Recogemos el token que vendra en la cabecera
        $token = $request->header('Authorization');
        $jwtAuth = new \App\Helpers\JwtAuth;

        // Pasa el token a la función checkToken
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            return $next($request);
        }else{
            $data = array(
                'code'    => 400,
                'status'  => 'error',
                'message' => 'El usuario no esta identificado.'
            );
            return response()->json($data, $data['code']);
        }
    }
}


// Registrar el Middleware en Kernel.php
