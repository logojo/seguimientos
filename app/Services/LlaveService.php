<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class LlaveService
{
    public function exchangeCodeForToken(string $code)
    {
        $url = config('llave.api_base') . '/oauth/obtenerToken';

        /** @var Response $response */
        $response = Http::withBasicAuth(config('llave.basic_user'), config('llave.basic_pass'))
            ->timeout(10)
            ->post($url, [
                'grantType'    => 'authorization_code',
                'code'         => $code,
                'redirectUri'  => config('llave.redirect_uri'),
                'clientId'     => config('llave.client_id'),
                'clientSecret' => config('llave.client_secret'),
            ]);

        if ($response->failed()) {
            Log::error('LlaveMX token error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json('accessToken') ?? null;
    }

    public function getDatosUsuario(string $token)
    {
        $url = config('llave.api_base') . '/oauth/datosUsuario';

        /** @var Response $response */
        $response = Http::withBasicAuth(config('llave.basic_user'), config('llave.basic_pass'))
            ->withHeaders(['accessToken' => $token])
            ->timeout(10)
            ->get($url);

        if ($response->failed()) {
            Log::error('LlaveMX datosUsuario error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function getRoles(string $token, $idUsuario)
    {
        $url = config('llave.api_base') . '/oauth/getRolesUsuarioLogueado';

        /** @var Response $response */
        $response = Http::withBasicAuth(config('llave.basic_user'), config('llave.basic_pass'))
            ->withHeaders(['accessToken' => $token])
            ->timeout(10)
            ->get($url, [
                'idUsuario' => $idUsuario,
                'idSistema' => config('llave.client_id'),
            ]);

        if ($response->failed()) {
            Log::error('LlaveMX getRoles error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    public function logout(string $token)
    {
        $url = config('llave.api_base') . '/oauth/cerrarSesion';
        /** @var Response $req */
        $req = Http::withBasicAuth(config('llave.basic_user'), config('llave.basic_pass'))
            ->withHeaders(['accessToken' => $token ?? ''])
            ->timeout(10)
            ->post($url, []);

        return $req->successful();
    }
}
