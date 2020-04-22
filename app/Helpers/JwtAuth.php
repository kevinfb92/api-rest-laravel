<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = '49svvv9jfs-p';
    }

    public function signIn($email, $password, $getIdentity = false) {
        //Buscar si existe el usuario con esas credenciales
        $user = User::where([
                    'email' => $email,
                    'password' => $password
                ])->first();

        //comprobar si son correctas
        $signIn = false;
        if (is_object($user)) {
            $signIn = true;
        }
        //Generar el token con los datos del usuario identificado
        if ($signIn) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            //Devolver los datos decodificados o el token, según si nos pasan el flag
            if (!$getIdentity) {
                $data = $jwt;   
            }else{
                $data = $decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto.'
            );
        }

        return $data;
    }
    
    public function checkToken($jwt, $getIdentity = false){
        $auth = false;
        
        try{
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);            
        } catch (Exception $ex) {
            $auth = false;
        }
        
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }
        else{
            $auth = false;
        }
        
        if($getIdentity){
            return $decoded;
        }
        
        return $auth;        
    }

}
