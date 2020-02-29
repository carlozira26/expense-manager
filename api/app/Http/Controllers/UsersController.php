<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class UsersController extends Controller
{
    private $response = array(
      "status" => false,
      "data" => array(),
      "message" => ""
    );

    public function fetch(){
      $user = User::select('id','fullname','email','role','created_at')->get();

      $this->response['data'] = $user;
      $this->response['status'] = true;
      return $this->response;
    }

    public function add(Request $request){
      $name = $request->input('name');
      $email = $request->input('email');
      $role = $request->input('role');
      $password = md5($request->input('password'));

      $user = new User();
      $user->fullname = $name;
      $user->email = $email;
      $user->role = $role;
      $user->password = $password;
      $user->save();
      

      if($user){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Created!";
      }
      return $this->response;
    }

    public function update(Request $request){
      $id = $request->input('id');
      $name = $request->input('name');
      $email = $request->input('email');
      $role = $request->input('role');
      $password = md5($request->input('password'));

      $user = user::where('id',$id)->update(array(
        'fullname' => $name,
        'email' => $email,
        'role' => $role,
        'password' => $password
      ));

      if($user){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Updated!";
      }
      return $this->response;
    }

    public function delete(Request $request){
      $id = $request->input('id');

      $user = User::where('id', $id)->delete();
      if($user){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Deleted!";
      }
      return $this->response;
    }

    public function changePass(Request $request){
      $userid = $request->input('userid');
      $oldpass = md5($request->input('oldpass'));
      $newpass = md5($request->input('newpass'));

      $user = User::where('id',$userid)->where('password',$oldpass)->first();
      if($user){
        $user = User::where('id',$userid)->update(array(
          'password' => $newpass
        ));
        $this->response['status'] = true;
        $this->response['message'] = "Password has been updated!";
      }else{
        $this->response['message'] = "Password Incorrect!";
      }
      return $this->response;
    }
}
