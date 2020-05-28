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
        $params = json_decode($json); // Devuelve objeto
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

            }
        // Si no nos llegan datos correctamente
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
    public function update(Request $request, $id)
    {
        //
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
}
