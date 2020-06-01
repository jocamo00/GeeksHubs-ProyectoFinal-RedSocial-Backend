<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{

    // Autenticacion que pide el header y token del usuario en cada petición excepto en index y show
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show','getImage']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Listar todos los posts
        $post = Post::all();

        dd($post);

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
            // Se ejecuta método que consigue usuario identificado
            $user = $this->getIdentity($request);

            // Validación, se le pasa array a validar y array con las validaciones deseadas
            $validate = Validator::make($params_array, [
                'title'       => 'required',
                'content'     => 'required',
                'image'       => 'required'
            ]);

            // Comprobación de que no hayan habido errores en la validación
            if($validate->fails()){
                $data = [
                    'code'    => 400,
                    'status'  => 'success',
                    'message' => 'No se ha guardado el post, faltan datos'
                ];
            }else{
                // Guarda el post
                $post = new Post();
                $post -> user_id     = $user   ->sub;
                $post -> title       = $params -> title;
                $post -> content     = $params -> content;
                $post -> image       = $params -> image;
                $post -> save();

                $data = [
                    'code'   => 200,
                    'status' => 'success',
                    'post'   => $post
                ];
            }

        }else{
            $data = [
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Datos incorrectos'
            ];
        }
        // Devuelve respuesta
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

                // Se ejecuta método que consigue usuario identificado
                $user = $this->getIdentity($request);

                if('user_id' != $user->sub){
                    $data = array(
                        'code'    => 400,
                        'status'  => 'error',
                        'message'    => 'No tienes acceso a este post'
                    );
                }

                // Buscar el registro a actualizar
                $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

                if(!empty($post) && is_object($post)){
                    // Actualizar el registro en concreto
                    $post->update($params_array);

                    $data = array(
                        'code'    => 200,
                        'status'  => 'succes',
                        'post'    => $params_array,
                        'changes' => $params_array
                    );
                }
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
     * @return \I"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQsImVtYWlsIjoicGVwaXRvQHBlcGl0by5jb20iLCJuYW1lIjoiUGVwZSIsInN1cm5hbWUiOiJQZXJleiIsImlhdCI6MTU5MDk0OTU4NiwiZXhwIjoxNTkxNTU0Mzg2fQ.YkR12T6XfZ_0lUCs2k_LZz_-b6gz-E2mUBSOFIIP64s"lluminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        // Se ejecuta método que consigue usuario identificado
        $user = $this->getIdentity($request);

        // Obtiene registro a eliminar
        // Que el id sea igual al id que me llega por la url
        // Que el user_id de la tabla de la BD sea igual al $user->sub que es el id que esta en el objeto user identificado
        $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

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

    // Método que consigue usuario identificado
    private function getIdentity($request){
        $jwtAuth = new JwtAuth();
        // Recoge el token que llega en la cabecera
        $token = $request->header('Authorization', null);
        // Devuelve el objeto decodificado del usuario
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request){
        // Recoge la imagen de ña petición
        $image = $request->file('file0');

        // Valida la imagen, le pasamos todos los dsatos y las validaciones que queramos
        $validate = Validator::make($request->all(), [
            'file0' => 'required | image | mimes:jpg,jpeg,png,gif'
        ]);

        // Comprueba que exista la imagen y que no haigan fallos de validación
        if(!$image || $validate->fails()){
            $data = [
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Error al subir la imagen'
            ];
        }else{
            // Guarda la imagen
            $image_name = time().$image->getClientOriginalName();
            Storage::disk('images')->put($image_name, File::get($image));

            $data = [
                'code'   => 200,
                'status' => 'success',
                'image'  => $image_name
            ];
        }
        return response()->json($data, $data['code']);
    }

    // Método para mostrar imagen
    public function getImage($filename){
        // Comprueba si existe la imagen
        $isset = Storage::disk('images')->exists($filename);

        if($isset){
            // Consigue la imagen
            $file = Storage::disk('images')->get($filename);

            // Devuelve la imagen
            return new Response($file, 200);
        }else{
            $data = [
                'code'    => 404,
                'status'  => 'error',
                'message' => 'La imagen no existe'
            ];
        }
        return  response()->json($data, $data['code']);
    }
}
