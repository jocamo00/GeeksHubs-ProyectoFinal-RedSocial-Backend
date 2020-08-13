# GeeksHubs-ProyectoFinal-RedSocial-Backend

_El proyecto es un Backend, que estructura una red social con una temática basada en viajes, se podrán realizar las siguientes funciones:
 * Los usuarios se podrán registrar y logear.
 * El usuario podra editar su perfil y subir su foto.
 * Se mostrará el perfil de cada usuario con sus publicaciones.
 * Se podrán realizar publicaciones.
 * El usuario que ha creado la publicación exclusivamente podrá editarla y eliminarla
 * Las publicaciones mostrarán una imagen, un texto, la foto y nombre de su autor y fecha de publicación.
 * Las publicaiones se mostrarán de forma que primero se muestre la más nueva.
 * Finalmente se podrá cerrar sesión._


Cuando el usuario se registra la contraseña es encriptada usando bcrypt, y se le envia un token al usuario. Cuando el usuario haga algún tipo de interacción con el servidor se verificara el token del mismo.


## Comenzando 🚀

_Para obtener una copia del proyecto en funcionamiento en tu máquina local para propósitos de desarrollo y pruebas, necesitaras descargarlo o clonar el repositorio a tu máquina._


### Tecnologías🛠️

Programas u Frameworks utilizados para el desarrollo y pruebas del proyecto:

* [VSCode] - Editor de código usado - (https://code.visualstudio.com/).
* [Laravel] - Framework de PHP.
* [Postman] - Herramienta para el envio de peticiones HTTP REST. (Para realizar pruebas)
* [GitHub] - Control de versiones.


### Instalación 🔧

Requiere [Composer](https://getcomposer.org/).

Una vez descargado, descomprimido y ubicado en el directorio del proyecto, instale las dependencias y devDependencies.

```sh
$ composer update
```

Inicie el servidor

```sh
$ php artisan serve
```

Las instrucciones sobre cómo usarlas en su propia aplicación están vinculadas a continuación.
GitHub  [plugins/github/README.md][PlGh] 


#### Código

Método para guardar un usuario en la base de datos
```sh
public function  store(Request $request)
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
```


Método para mostrar un post
```sh
/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id)->load('user'); // Para que saque tambien la propiedad de usuario

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
```


Rutas
```sh
// Rutas del controlador de usuarios
Route::apiResource('/user', 'UserController');
Route::put('/user/update', 'UserController@update');
Route::post('/login', 'UserController@login');
Route::post('/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/user/avatar/{filename}', 'UserController@getImage');
Route::get('/user/detail/{id}', 'UserController@detail');

// Rutas del controlador de posts
Route::apiResource('post', 'PostController');
Route::post('/post/upload', 'PostController@upload');
Route::get('/post/image/{filename}', 'PostController@getImage');
Route::get('/post/user/{id}', 'PostController@getPostsByUser');

// Rutas del controlador de comentario
Route::apiResource('/comment', 'CommentController');
```

## Video
(https://www.youtube.com/watch?v=lQzBlWq1LxU&t=1s)

## Wiki 📖

Puedes encontrar mucho más de cómo utilizar este proyecto en nuestra [Wiki](https://github.com/jocamo00/GeeksHubs-ProyectoFinal-RedSocial-Backend.git)

La parte de frontend de este proyecto la puedes encontrar aquí. [Wiki](https://github.com/jocamo00/GeeksHubs-ProyectoFinal-RedSocial-Frontend.git)

## Versionado 📌

Usamos [GitHub](https://github.com/) para el versionado. Para todas las versiones disponibles, mira los [tags en este repositorio](https://github.com/jocamo00/GeeksHubs-ProyectoFinal-RedSocial-Backend.git).

## Autor ✒️

* **Jose Carreres** - *Todo el trabajo* - [jocamo00](https://github.com/jocamo00)

## Licencia 📄

Este proyecto está bajo la Licencia http://www.apache.org/licenses/LICENSE-2.0








