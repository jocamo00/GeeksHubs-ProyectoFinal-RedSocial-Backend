<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;


class JwtAuth{

    public $key;

    public function __construct() {
        $this->key = '0000';
    }

    // Tendrá como parámetro email y password que son las credenciales que se comprobarán
    // El parámetro getToken lo usaremos si queremos devolver el usuario identificado ya
    public function signup($email, $password, $getToken = null){
        //Comprueba si existe un usuario que conincida con el email y password que se esta pasando
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first(); // Solo quiero que me saque uno

        // Comprueba si las credenciales son correctas (objeto)
        $signup = false;
        // Si el usuario es un objeto se habrá identificado correctamente
        // Y signup será true
        if(is_object($user)){
            $signup = true;
        }

        // Si signup es true, genera el token con los datos del usuario identificado
        if($signup){
            // Array con los datos que quiero guardar del usuario
            $payload = array(
                'sub'     => $user->id,                   // sub hace refencia al id del usuario
                'email'   => $user->email,
                'name'    => $user->name,
                'surname' => $user->surname,
                'iat'     => time(),                      // Fecha en la que se crea el token
                'exp'     => time() + (7 * 24 * 60 *60)   // Fecha caducidad del token, en este caso una semana
            );

            // se generá el token
            // Se le indica el token que queremos codificar,
            // una clave (key) y el algoritmo de codificación
            $jwt = JWT::encode($payload, $this->key, 'HS256');

            // Decodificación del token, se obtiene un objeto con la información del token
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            // Devolver los datos decodificados o el token, en función del parámetro $getToken
            // Si $getToken es nulo que me devuelva el token
            if(is_null($getToken)){
                $data = $jwt;

            // Y si no que me devuelva la decodificación del token
            }else{
                $data = $decoded;
            }

        }else{
            $data = array(
                'status'  => 'error',
                'message' => 'Login incorrecto.'
            );
        }

        return $data;
    }

    // Comprueba si el token es correcto y devuelve los datos del usuario identificado
    // Le pasamos el token que queremos decodificar y
    // getIdentity que si viene true devolvera el usuario
    public function checkToken($jwt, $getIdentity = false){
        $auth = false;


        try {
            // Se quitan las comillas dobles si vinieran con el token, remplazandolas por vacio
            $jwt = str_replace('"', '', $jwt);

            // Decodificación del token
            // Se le pasa el jwt, la key y el algoritmo de cifrado
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }

        // Si decoded no esta vacio, es un objeto y tenemos el id del usuario en el token
        // Devuelve la Autenticación
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        // Si llega getIdentity devuelve el token decodificado (el objeto con los datos de usuario)
        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }

}
