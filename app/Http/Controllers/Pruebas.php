<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

class Pruebas extends Controller
{
    public function index(){
        return '<h2>Probando</h2>';
    }
    
    public function testORM(){
        $users = User::All();
        return $users;
    }
}
