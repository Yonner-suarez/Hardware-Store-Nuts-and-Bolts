<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class generarController extends Controller
{
    function generarToken(Request $request)
{
    // Obtener el secreto JWT desde la configuración
    $secret = config('app.jwt');

    // Crear el payload del token con los datos de la request
    $payload = [
        'iss' => 'http://localhost', // Emisor
        'iat' => time(), // Tiempo de emisión
        'exp' => time() + 3600, // Tiempo de expiración (1 hora)
        'sub' => $request->input('usuario_id', 'usuario_ejemplo'), // Sujeto
        'datos' => $request->all() // Datos adicionales
    ];

    // Codificar el encabezado
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $headerEncoded = base64_encode($header);

    // Codificar el payload
    $payloadEncoded = base64_encode(json_encode($payload));

    // Crear la firma
    $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
    $signatureEncoded = base64_encode($signature);

    // Crear el token JWT
    $jwt = "$headerEncoded.$payloadEncoded.$signatureEncoded";

    // Retornar el token JWT
    return response()->json(['token' => $jwt]);
}
}
