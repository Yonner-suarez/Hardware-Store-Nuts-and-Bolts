<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateJWT
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        $secret = config('app.jwt');
        
        try {
            $partes = explode('.', $token);
            if (count($partes) != 3) {
                throw new \Exception('Formato de token inválido');
            }

            list($header, $payload, $signature) = $partes;

            $signatureValida = hash_hmac('sha256', "$header.$payload", $secret, true);
            $signatureValida = base64_encode($signatureValida);

            if ($signature !== $signatureValida) {
                throw new \Exception('Firma inválida');
            }

            $payload = json_decode(base64_decode($payload), true);
            
            if ($payload['exp'] < time()) {
                throw new \Exception('Token expirado');
            }

            // Token válido, continuar con la solicitud
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
