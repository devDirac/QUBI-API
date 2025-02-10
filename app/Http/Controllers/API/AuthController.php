<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Utils\MailSend;
use Illuminate\Support\Facades\DB;
use App\Models\Tokens;
use App\Models\ProcesosTokens;
use Illuminate\Support\Facades\Http;
use App\Models\JunoProyectos;
use App\Models\JunoProyectosUsuarios;

class AuthController extends BaseController
{

    public $mailValidation = 'required|email';
    public $invalidFormatMessage = 'Formato invalido';

    /* Registro de usuario */
    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'usuario' => 'required',
                'email' => 'required',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($this->invalidFormatMessage, $validator->errors());
            }
            $exist = User::where('usuario', $request->usuario)->get()->first();
            if ($exist) {
                return $this->sendError('El usuario ingresado ya fue dado de alta anteriormente', [], 500);
            }
            $userLog = Auth::user();
            $input1['name'] = $request->name;
            $input1['email'] = $request->email;
            $input1['password'] = bcrypt($request->password);
            $input1['usuario'] = $request->usuario;
            $input1['telefono'] = $request->has('telefono') ? $request->telefono : null;
            $input1['foto'] = $request->has('foto') ? $request->foto : '';
            $input1['activo'] = 1;
            $input1['empresa'] = $request->has('empresa') ? $request->empresa : '';
            $sendMail = new MailSend();
            $mail = $sendMail->sendMailPro([
                'email' => $request->email,
                'titulo' => '<h1 style="color:#F89E44; font-family: var(--bs-font-sans-serif);">' . $request->name . '</h1><h3 style="color:#38425d; font-weight: bold; font-family: var(--bs-font-sans-serif);">QUBI te notifica</h3>',
                'html' => '<h3 style="color:#38425d; font-weight: bold; font-family: var(--bs-font-sans-serif);">Tu registro en la plataforma JUNO fue exitosa</h3>',
            ], 'mail', "Registro exitoso");
            $user = User::create($input1);
            return $this->sendResponse($user);
        } catch (\Throwable $th) {
            return $this->sendError('Error al registrar el usuario', $th, 500);
        }
    }

    /* inicio de sesión */
    public function signin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario' => 'required',
                'password' => 'required',
                'valueCaptcha' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError($this->invalidFormatMessage, $validator->errors());
            }
            $secret = env('GOOGLE_SCECRET_KEY', '');
            $response = Http::post("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$request->valueCaptcha}")->throw()->json();
            if($response['success'] !== true){
                return $this->sendError('El captcha proporcionado ya expiro, reinicie el captcha e intente de nuevo', ['error' => ''], 500);
            }
            if (Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'activo' => 1])) {
                $user = Auth::user();
                $proyectos = JunoProyectos::join('juno_proyectos_usuarios', 'juno_proyectos.id', '=', 'juno_proyectos_usuarios.id_proyecto')
                ->select('juno_proyectos.*')
                ->where('juno_proyectos_usuarios.id_usuario', $user->id)
                ->where('juno_proyectos.activo', 1)
                ->get();
                $user['proyectos'] = $proyectos;

                $users['data'] = $user;
                $token = $user->createToken('MyAuthApp');
                $users['token'] = $token->plainTextToken;
                unset($user->created_at);
                unset($user->updated_at);
                return $this->sendResponse($users);
            } else {
                return $this->sendError('La contraseña o el usuario son incorrectos o el usuario ya fue dado de baja', ['error' => ''], 401);
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error al iniciar sesión', $th, 500);
        }
    }

    /* refresca el usuario por sesion inactiva */
    public function getUserRefrsh(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $proyectos = JunoProyectos::join('juno_proyectos_usuarios', 'juno_proyectos.id', '=', 'juno_proyectos_usuarios.id_proyecto')
            ->select('juno_proyectos.*')
            ->where('juno_proyectos_usuarios.id_usuario', $user->id)
            ->where('juno_proyectos.activo', 1)
            ->get();
            $user['proyectos'] = $proyectos;
            $users['data'] = $user;
            $users['token'] = $request->token;
            unset($user->created_at);
            unset($user->updated_at);
            return $this->sendResponse($users);
        } catch (\Throwable $th) {
            return $this->sendError('Error al recuperar los datos del usuario', $th, 500);
        }
    }

    /* cierra la sesion */
    public function logOut(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            Auth::guard('web')->logout();
            return $this->sendResponse('Cierre de sesión exitoso.');
        } catch (\Throwable $th) {
            return $this->sendError('Error al cerrar la sesión del usuario', $th, 500);
        }
    }

    /* inicia el proceso para la recuperación de la contraseña */
    public function passwordRecoverSendLink(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'correo' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El correo es requerido', $validator->errors(), 409);
            }
            $user = User::where('email', $input['correo'])->get()->first();
            if (!$user) {
                return $this->sendError('Cuenta de correo no registrada', 'error', 404);
            }
            $procesoId = ProcesosTokens::where('proceso', 'recuperar password')->get()->first();

            $tokenValidation = Tokens::where('id_token_proceso', $procesoId->id)->where('id_usuario', $user->id)->get()->first();
            if ($tokenValidation) {
                return $this->sendError('Ya tienes un proceso referente a esta solicitud, verifica tu correo, si no lo encuentras revisa tu carpeta de spam, este proceso se puede volver a realizar cada 24 horas', "error", 500);
            }
            $token = bcrypt($user->email . $user->id);
            $tokenAdd['token'] = $token;
            $tokenAdd['id_usuario'] = $user->id;
            $tokenAdd['id_token_proceso'] = $procesoId->id;
            $user = Tokens::create($tokenAdd);
            $sendMail = new MailSend();
            $mail = $sendMail->sendMailPro([
                'email' => $input['correo'],
                'titulo' => '<h1 style="color:#F89E44; font-family: var(--bs-font-sans-serif);">' . $user->name . '</h1><h3 style="color:#38425d; font-weight: bold; font-family: var(--bs-font-sans-serif);">QUBI te notifica</h3>',// "Hola ".$user->name,
                'html' => '<h3 style="color:#38425d; font-weight: bold; font-family: var(--bs-font-sans-serif);">Haz iniciado el proceso para la recuperación de tu contraseña' . "<br>" . 'para reestablecer tu contraseña da click ' . "<br>" . '<a href="http://localhost:3000/recupera-password-validacion?token=' . $token . '">aqui</a></h3><br><br><br><p style="color:#38425d; font-weight: bold; font-family: var(--bs-font-sans-serif);">Tienes un plazo de 24hrs para reestablecer tu conmtraseña</p>',
            ], 'mail', "Recuperación de contraseña");
            return $mail;
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    /* Valida el token que se genero para recuperar la contraseña */
    public function passwordRecoverTokenValidation(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'token' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El token es requerido', $validator->errors(), 409);
            }
            $tokenValidation = Tokens::where('token', $input['token'])->get()->first();
            if (!$tokenValidation) {
                return $this->sendError('Este token no es válido o ya fue utilizado', "error", 400);
            }
            return $this->sendResponse($tokenValidation);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    /* actualiza la contraseña */
    public function passwordReset(Request $request, User $usuario)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'contrasena' => 'required',
                'contrasenaConfirm' => 'required',
                'token' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('La contraseña, la confirmación de la contraseña y el token son requeridos', $validator->errors(), 409);
            }
            if ($input['contrasena'] !== $input['contrasenaConfirm']) {
                return $this->sendError('La contraseña y la confirmación de la contraseña no son iguales', $validator->errors(), 400);
            }
            $infoTokenUser = DB::table('tokens')
                ->join('users', 'users.id', '=', 'tokens.id_usuario')
                ->select('tokens.id', 'tokens.token', 'tokens.id_usuario', 'users.name', 'users.email')
                ->where('tokens.token', $input['token'])->get()->first();
            if (!$infoTokenUser) {
                return $this->sendError('No existe relación del token con el usuario', [], 404);
            }
            $update['password'] = bcrypt($input['contrasena']);
            $usuario->where('id', '=', $infoTokenUser->id_usuario)->update($update);
            DB::table('tokens')->where('token', $input['token'])->delete();
            return $this->sendResponse('Se ha actualizado la contraseña con éxito.');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function passwordResetSinToken(Request $request, User $usuario)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'contrasena' => 'required',
                'contrasenaConfirm' => 'required',
                'id' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('La contraseña, la confirmación de la contraseña y el token son requeridos', $validator->errors(), 409);
            }
            if ($input['contrasena'] !== $input['contrasenaConfirm']) {
                return $this->sendError('La contraseña y la confirmación de la contraseña no son iguales', $validator->errors(), 400);
            }
            $update['password'] = bcrypt($input['contrasena']);
            $usuario->where('id', '=', $request->id)->update($update);
            return $this->sendResponse('Se ha actualizado la contraseña con éxito.');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    /* Obtiene todos los usuarios */
    public function getUsers()
    {
        try {
            $users = User::get();
            return $this->sendResponse($users);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }
    
    /* edita la información de un usuario */
    public function editUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($this->invalidFormatMessage, $validator->errors());
            }
            $user = User::find($request->id);
            if (!$user) {
                return $this->sendError('Este usuario no existe', [], 404);
            }
            $user->name =  $request->has('name') ? $request->name : $user->name;
            $user->empresa = $request->has('empresa') ? $request->empresa : $user->empresa;
            $user->telefono = $request->has('telefono') ? $request->telefono : $user->telefono;
            $user->foto = $request->has('foto') ? $request->foto : $user->foto;
            $user->save();
            return $this->sendResponse("Se ha actualizado el usuario con éxito");
        } catch (\Throwable $th) {
            return $this->sendError('El correo ingresado ya fue dado de alta anteriormente', $th, 500);
        }
    }

    /* activa el usuario */
    public function setActiveUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($this->invalidFormatMessage, $validator->errors());
            }
            $user = User::find($request->id);
            if (!$user) {
                return $this->sendError('Este usuario no existe', 'error', 404);
            }
            $user->activo = !$user->activo;
            $user->save();
            return $this->sendResponse($user);
        } catch (\Throwable $th) {
            return $this->sendError('El correo ingresado ya fue dado de alta anteriormente', $th, 500);
        }
    }

}
