<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usuarios\Bitacora;
use Illuminate\Http\Request;
use App\Services\LlaveService;
use App\Services\UserAssigmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LlaveController extends Controller
{
    protected $llave;

    public function __construct(LlaveService $llave)
    {
        $this->llave = $llave;
    }

    public function redirectToLlave()
    {
        $state = Str::random(32);
        Session::put('llave_state', $state);

        $query = http_build_query([
            'client_id'    => config('llave.client_id'),
            'redirect_url' => config('llave.redirect_uri'),
            'state'        => $state,
        ]);

        return redirect(config('llave.auth_url') . '?' . $query);
    }

    public function callback(Request $request)
    {
        if ($request->input('state') !== Session::get('llave_state')) {
            abort(403, 'Estado inválido');
        }

        $code = $request->input('code');
        
        if (!$code) {
            return response('Código no provisto', 400);
        }

        $token = $this->llave->exchangeCodeForToken($code);

        if (!$token) {
            return response('Error al obtener token (posible expiración del code)', 400);
        }

        $datos = $this->llave->getDatosUsuario($token);
        if (!$datos) {
            return response('Error al obtener datos del usuario', 400);
        }


        $user = User::where([
                             ['curp', $datos['curp'] ],
                             ['llave_id', $datos['idUsuario'] ]
                            ])->first();

        try {
            if(!$user ) {

                // crear usuario local 
            $user = User::withoutEvents(function () use ($datos) {
                    return User::create([
                        'curp'     => $datos['curp'] ?? null,
                        'name'     => ($datos['nombre'] ?? '') . ' ' . ($datos['primerApellido'] ?? ''),
                        'email'    => $datos['correo'] ?? null,
                        'password' => bcrypt($datos['curp']),
                        'llave_id' => $datos['idUsuario'] ?? null,
                    ]);
                });

            }

             //se le asigna el rol de consulta si es un usuario nuevo
            if ($user->wasRecentlyCreated) {
                if ($user->curp == config('llave.root_curp')) {
                    $user->assignRole('root');

                    $assignmentService = app(UserAssigmentService::class);
                    
                    $assignmentService->assign(
                        user:         $user,
                       // municipio_id: null,
                        perfil:       Role::findByName('root'),
                        assignedBy:   User::find(1),                        
                        cargo:        null
                    );

                    $this->registrarCreacionUsuario($user);
                } else {
                    $user->assignRole('invitado');
                }
            }

        } catch (\Throwable $th) {
            throw $th;
        }


        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        Auth::login($user);
        $request->session()->regenerate();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Session::put('llave_access_token', $token);

        return redirect('/dashboard');
    }

    //*se manejara los roles de manera local
    public function getRoles()
    {
        $token = Session::get('llave_access_token');
        $user = Auth::user();

        return $this->llave->getRoles($token, $user->llave_id ?? null);
    }

    public function logout()
    {
        $token = Session::get('llave_access_token');
        try {
            $this->llave->logout($token);
        } catch (\Exception $e) {
            //Registrar error pero continuar cerrando sesión localmente
            logger()->error('LlaveMX logout error: ' . $e->getMessage());
        }

        Session::forget('llave_access_token');
        Auth::logout();

        return redirect('/');
    }

    private function registrarCreacionUsuario(User $user): void
    {
        try {
            $assignmentService = app(UserAssigmentService::class);
            $assignment = $assignmentService->currentAssignment($user);

            if (!$assignment) return;

            $record = new Bitacora();
            $record->accion = 'created';
            $record->user_assignment_id = $assignment->id;
            $user->bitacora()->save($record);

        } catch (\Exception $e) {
            // Log del error sin interrumpir el flujo de login
            Log::warning('No se pudo registrar bitácora del nuevo usuario: ' . $e->getMessage());
        }
    }
}
