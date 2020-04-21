<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de pruebas de User Controller";
    }
    public function register(Request $request){
        return "Accion de registro de User Controller";
    }    
    public function login(Request $request){
        return "Accion de login de User Controller";
    }        
    
}
