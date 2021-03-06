<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    // Tabla que va a utilizar de la base de datos
    protected $table = 'likes';

    // Relación de uno a muchos pero inversa (muchos a uno)
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    // Relación de uno a muchos pero inversa (muchos a uno)
    public function post(){
        return $this->belongsTo('App\Post', 'post_id');
    }
}
