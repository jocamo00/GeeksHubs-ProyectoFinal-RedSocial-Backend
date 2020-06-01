<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Tabla que va a utilizar de la base de datos
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'image'
    ];


    // Relación de uno a muchos pero inversa (muchos a uno)
    // Muchos pots pueden ser creados por un usuario
    // Saca el objeto del usuario al cual esta relacionado el user_id
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
