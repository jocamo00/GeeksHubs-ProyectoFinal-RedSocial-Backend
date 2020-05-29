<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post = Post::all();

        // Devuelve json con mensaje y los posts
        return response()->json([
            'code'   => 200,
            'status' => 'success',
            'posts'  => $post
        ], 200);
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
        // Recoger datos por post
        $json = $request->input('json', null);

        // Decodifica el json
        $params = json_decode($json); // Devuelve objeto
        $params_array = json_decode($json, true); // Devuelve array

        // Comprobación si nos han llegado datos correctamente
        if(!empty($params_array)){

        }else{

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if(is_object($post)){
            $data = [
                'code'   => 200,
                'status' => 'success',
                'posts'  => $post
            ];
        }else{
            $data = [
                'code'    => 404,
                'status'  => 'error',
                'message' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
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
        // Recoger los datos por post
        $json =$request->input('json', null);

        // Decodifica el json en un array
        $params_array = json_decode($json, true);

        // Comprobación si nos han llegado datos correctamente
        if(!empty($params_array)){
            // Validación, se le pasa array a validar y array con las validaciones deseadas
            $validate = Validator::make($params_array, [
                'title'       => 'required',
                'content'     => 'required'
            ]);

            // Comprobación de que no hayan habido errores en la validación
            if($validate->fails()){
                $data = [
                    'code'    => 400,
                    'status'  => 'success',
                    'message' => 'Post incorrecto'
                ];
            }else{
                // Eliminar lo que no queremos actualizar
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);

                // Actualizar el registro en concreto
                // Comprueba que el campo id es igual al id que llega por la url
                $post = Post::where('id', $id)->update($params_array);

                $data = array(
                    'code'    => 200,
                    'status'  => 'succes',
                    'post'    => $params_array,
                    'changes' => $params_array
                );
            }
        }else{
            $data = [
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Datos enviados incorrectamente'
            ];
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
        // Obtiene registro a eliminar
        $post = Post::find($id);

        if(!empty($post)){
            //Borra el post
            $post->delete();

            $data = [
                'code'   => 200,
                'status' => 'succes',
                'post'   => $post
            ];
        }else{
            $data = [
                'code'    => 404,
                'status'  => 'error',
                'message' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
