<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Recoger los datos del usuario por post
        // Si no llegara el parámetro, daria null
        $json = $request->input('json', null);
        //dd($json);

        // Se decodifica el JSON que ha llegado
        $params       = json_decode($json); // Devuelve objeto
        $params_array = json_decode($json, true); // Devuelve array
        //dd($params_array);

        // Si llegan datos hace la validación
        if(!empty($params) && !empty($params_array)){

            // Elimina espacios en blanco
            $params_array = array_map('trim', $params_array);

            // Validar datos
            // Se pasa el array con los campos que se van a validar
            // Se pasan las validaciones que queremos hacer
            $validate = Validator::make($params_array, [
                'name'     => 'required | alpha',
                'surname'  => 'required | alpha',
                'email'    => 'required | email | unique:users', // Necesita la tabla
                'password' => 'required'
            ]);


            // Comprueba si ha habido algún fallo en la validación
            if($validate->fails()){
                // En caso de error en el registro devolvemos un mensaje con el error
                $data    = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors'  => $validate->errors()
                );
            }else{
                // Cifrado de la contraseña,laravel apiResources se usa el método hash()
                // Se le pasa algoritmo de cifrado y el password
                $pwd = hash('sha256', $params->password);

                // Crea el usuario
                $user = new User();
                $user -> name     = $params_array['name'];
                $user -> surname  = $params_array['surname'];
                $user -> email    = $params_array['email'];
                $user -> password = $pwd;
                $user -> role     = 'ROLE_USER';
                //dd($user);

                // Guarda el usuario
                $user->save();

                // Mensaje de registro ok
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );

            }
        // Mensaje si no nos llegan datos correctamente
        }else{
            $data    = array(
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        // Comprobar si el usuario esta identificado
        // Recogemos el token que vendra en la cabecera
        $token = $request->header('Authorization');
        $jwtAuth = new \App\Helpers\JwtAuth;

        // Pasa el token a la función checkToken
        $checkToken = $jwtAuth->checkToken($token);

        // Recoje los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true); // Devuelve un array

        // Comprueba que existan datos
        if($checkToken && !empty($params_array)){
            // Actualiza el usuario

            // Sacar usuario identificado mediante el parametro getIdentity a true
            $user = $jwtAuth->checkToken($token, true);

            // Validar datos
            $validate = Validator::make($params_array, [
                'name'     => 'required | alpha',
                'surname'  => 'required | alpha',
                // Usuario cuya excepción de email se puede producir
                // Si no se cambia el email no dara error al actualizar
                'email'    => 'required | email | unique:users,'.$user->sub, // Necesita la tabla
            ]);

            // Se quitan los campos que no se quieren actualizar
            unset($params_array['id']);
            unset($params_array['rol']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            // Se actualiza el usuario cuyo id sea igual al id que tengo en $user->sub
            $user_update = User::where('id', $user->sub)->update($params_array);

            // Devuelve array con los resultados
            $data = array(
                'code'    => 200,
                'status'  => 'success',
                'user'    => $user,
                'changes' => $params_array // Muestra el usuario con los nuevos datos
            );
        }else{
            $data = array(
                'code'    => 400,
                'status'  => 'error',
                'message' => 'El usuario no esta identificado.'
            );
        }
        return response()->json($data, $data['code']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Login de usuarios
    public function login(Request $request){

        $jwtAuth = new \App\Helpers\JwtAuth;

        // Recibe datos por post
        $json = $request -> input('json', null);

        // Decodifica los datos
        $params       = json_decode($json);       // Devuelve un objeto
        $params_array = json_decode($json, true); // Devuelve un array

        // Valida los datos
        $validate = Validator::make($params_array, [
            'email'    => 'required | email',
            'password' => 'required'
        ]);

        // Si falla la validación
        if($validate->fails()){
            $signup = array(
                'status'  => 'error',
                'code'    => '404',
                'message' => 'El usuario no se ha podido identificar',
                'errors'  => $validate->errors()
            );
        }else{
            // Cifra la contraseña que llega por parámetro
            $pwd = hash('sha256', $params->password);

            // Devuelve el token mediante el método signup()
            $signup = $jwtAuth->signup($params->email, $pwd);

            // Si existe el parámetro getToken devuelde los datos decodificados
            if(!empty($params->getToken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        // Devuelve los datos en formato JSON
        return response()->json($signup, 200);
    }

    // Función para subir el avatar del usuario
    public function upload(Request $request){
        $data = array(
            'code'    => 400,
            'status'  => 'error',
            'message' => 'Error al subir imagen'
        );
        return response()->json($data, $data['code']);
    }
}
