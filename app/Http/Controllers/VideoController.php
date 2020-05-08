<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Video;

class VideoController extends Controller
{
    
    public function create(Request $request){
        $data = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'El video no se ha creado'
        );        
      
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
        $user_id = $user->sub;

        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array
        
        if(!empty($params) && !empty($params_array)){
            $validate = \Validator::make($params_array, [
                'title'=>'required',
                'description'=>'required',
                'url'=>'required|url'
            ]); 
            
            if(!$validate->fails()){
                $video = new Video();
                $video->title = $params_array['title'];
                $video->url = $params_array['url'];
                $video->description = $params_array['description'];
                $video->user_id = $user_id;
                $video->status = 'normal';
                
                $video->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El video se ha creado correctamente',
                    'video' => $video
                );     
            }
            else{
                $errors = $validate->errors();
                $data['message'] = $errors;
            }
                      
        }
        
        return response()->json($data, $data['code']);
    }
    
     public function update(Request $request, $id){
        $data = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'El video no se ha actualizado'
        );        
      
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
        $user_id = $user->sub;

        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array
        
        if(!empty($params) && !empty($params_array)){
            $validate = \Validator::make($params_array, [
                'title'=>'required',
                'description'=>'required',
            ]);         
            
            if(!$validate->fails()){
                $video = Video::find($id);
                if(is_object($video)){
                    if($video.user_id == $user_id){
                       $video->title = $params->title;
                       $video->description = $params->description;
                       $video->save();
                  
                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Video actualizado',
                            'video' => $video
                        );                        
                    }
                }
            }
            
            
        }      
     }
    
    public function detail(Request $request, $id = null){
        $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El video no se ha encontrado'
        );     
        
        if($id != null){
            $video = Video::find($id);
            if(is_object($video)){
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Video encontrado',
                    'video' => $video
                );                 
            }
        }
        else{            
            $data['message'] = 'Id invalida';
        }

        return response()->json($data, $data['code']);
    }
    
    public function remove(Request $request, $id = null){
         $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El video no se ha encontrado'
        );            
         
         if($id != null){
                          
            $video = Video::find($id);
            if(is_object($video)){
                $data = array(
                  'status' => 'success',
                  'code' => 200,
                  'message' => 'Video borrado',
                  'video' => $video
                );                    
                $video->delete();
            }
         }
         else{
            $data['message'] = 'Id invalida';
         }
         
         return response()->json($data, $data['code']);
    }
    
    public function listAll(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
        $id = $user->sub;
        $videos = Video::where('user_id', $id)->orderBy('id', 'desc')->paginate(5);
        return $videos;
    }
    
    
}
