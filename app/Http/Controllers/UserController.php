<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller {

    public function pruebas(Request $request) {
        return "Accion de pruebas de User Controller";
    }

    public function register(Request $request) {
        //recoger datos
        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array

        if (!empty($params) && !empty($params_array)) {

            //limpiar datos (quitar espacios)
            $params_array = array_map('trim', $params_array);


            //validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                         //comprobar duplicados: unique:users
                        'email' => 'required|email|unique:users',
                        'password' => 'required'
            ]);
            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                //validacion pasada correctamente

                
                //cifrar contrase�a
                $pwd = hash('sha256', $params->password);
                
                //crear usuario               
                $user = new User();
                $user->name = $params->name;
                $user->surname = $params->surname;
                $user->email = $params->email;
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                
                //guardar el usuario
                $user->save();
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        }
        else{
            $data = array(
              'status' => 'error',
              'code' => 404,
              'message' => 'Los datos enviados no tienen el formato correcto',
              'json' => $request->input('json')
            );          
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $jwtAuth = new \JwtAuth();     
        
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);   //array format for the validator
                
        //validar datos
        $validate = \Validator::make($params_array, [                    
                    'email' => 'required',
                    'password' => 'required'
        ]);
        if ($validate->fails()) {
            $signIn = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            //validacion pasada correctamente        
        

            $pwd = hash('sha256', $params->password);
            $signIn = $jwtAuth->signIn($params->email, $pwd);
            
            if(isset($params->gettoken)){
                $signIn = $jwtAuth->signIn($params->email, $pwd, true);           
            }
        }
        return response()->json($signIn, 200);
    }
    
    public function update(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
        //get input
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if($checkToken && !empty($params_array)){         
           
            //get identified user
            $user = $jwtAuth->checkToken($token, true);
            
            //validate data
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                 //comprobar que el email no lo tiene otro usuario ya,
                //concateno a las condiciones el id del user propio como excepcion
                'email' => 'required|email|unique:users,'.$user->sub
            ]);
                                    
            //remove data we dont need
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            //update user
            $user_update = User::where('id', $user->sub)->update($params_array);
            
            //return array with the results
            
            $user = User::find($user->sub);
            
           $data = array(
                'code'      => 200,
                'status'    => 'success',
                'user'      => $user
            );              
        }
        else{           
            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'El usuario no esta identificado'
            );          
        }
        
        return response()->json($data, $data['code']);
        
    }
    
    public function detail($id){
        $user = User::find($id);
        
        if(is_object($user)){
            $data = array(
                'code'  => 200,
                'status'=> 'success',
                'message'=> $user
            );
        }
        else{
             $data = array(
                'code'  => 400,
                'status'=> 'error',
                'message'=> 'El usuario no existe'
            );           
        }
        
        return $data;
    }

}
