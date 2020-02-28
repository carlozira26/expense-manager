<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class LoginController extends Controller
{
    private $response = array(
      "status" => false,
      "data" => array(),
      "message" => ""
    );

    public function login(Request $request){
      $email = $request->input('email');
      $password = $request->input('password');

      $user = User::where('email',$email)
        ->where('password',md5($password))->first();

      if($user){
        $this->response['data'] = $user;
        $this->response['status'] = true;
      }
      return $this->response;
    }
}
