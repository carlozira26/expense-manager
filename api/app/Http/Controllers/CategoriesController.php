<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Category;

class CategoriesController extends Controller
{
    private $response = array(
      "status" => false,
      "data" => array(),
      "message" => ""
    );

    public function fetch(){
      $categories = Category::select('id','category','description','created_at')->get();

      $this->response['data'] = $categories;
      $this->response['status'] = true;
      return $this->response;
    }

    public function add(Request $request){
      $categoryname = $request->input('categoryname');
      $description = $request->input('description');

      $category = new Category();
      $category->category = $categoryname;
      $category->description = $description;
      $category->save();
      
      if($category){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Created!";
      }
      return $this->response;
    }

    public function update(Request $request){
      $id = $request->input('id');
      $categoryname = $request->input('categoryname');
      $description = $request->input('description');

      $category = Category::where('id',$id)->update(array(
        'category' => $categoryname
      ));

      if($category){
        $this->response['data'] = $category;
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Updated!";
      }
      return $this->response;
    }

    public function delete(Request $request){
      $id = $request->input('id');

      $category = category::where('id', $id)->delete();
      if($category){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Deleted!";
      }
      return $this->response;
    }
}
