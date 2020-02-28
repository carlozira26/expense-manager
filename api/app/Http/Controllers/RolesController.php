<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Role;

class RolesController extends Controller
{
    private $response = array(
      "status" => false,
      "data" => array(),
      "message" => ""
    );

    public function fetch(){
      $role = Role::select('id','role','description','created_at')->get();

      $this->response['data'] = $role;
      $this->response['status'] = true;
      return $this->response;
    }

    public function add(Request $request){
      $rolename = $request->input('role');
      $description = $request->input('description');

      $role = new Role();
      $role->role = $rolename;
      $role->description = $description;
      $role->save();
      
      if($role){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Created!";
      }
      return $this->response;
    }

    public function update(Request $request){
      $id = $request->input('id');
      $rolename = $request->input('role');
      $description = $request->input('description');

      $role = Role::where('id',$id)->update(array(
        'role' => $rolename,
        'description' => $description,
      ));

      if($role){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Updated!";
      }
      return $this->response;
    }

    public function delete(Request $request){
      $id = $request->input('id');

      $role = Role::where('id', $id)->delete();
      if($role){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Deleted!";
      }
      return $this->response;
    }
}
