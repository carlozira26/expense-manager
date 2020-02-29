<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Expense;

class ExpensesController extends Controller
{
    private $response = array(
      "status" => false,
      "data" => array(),
      "message" => ""
    );

    public function fetch(){
      $userid = $_GET['userid'];
      $expense = Expense::select('id','expense','amount','entrydate','created_at')
        ->where('userid', $userid)
        ->get();

      $this->response['data'] = $expense;
      $this->response['status'] = true;
      return $this->response;
    }

    public function add(Request $request){
      $expensename = $request->input('expense');
      $amount = $request->input('amount');
      $entrydate = $request->input('entrydate');
      $userid = $request->input('userid');

      $expense = new Expense();
      $expense->userid = $userid;
      $expense->expense = $expensename;
      $expense->amount = $amount;
      $expense->entrydate = $entrydate;
      $expense->save();
      
      if($expense){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Created!";
      }
      return $this->response;
    }

    public function update(Request $request){
      $id = $request->input('id');
      $expensename = $request->input('expense');
      $amount = $request->input('amount');
      $entrydate = $request->input('entrydate');
      $userid = $request->input('userid');

      $expense = Expense::where('id',$id)->update(array(
        'userid' => $userid,
        'expense' => $expensename,
        'amount' => $amount,
        'entrydate' => $entrydate,
      ));

      if($expense){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Updated!";
      }
      return $this->response;
    }

    public function delete(Request $request){
      $id = $request->input('id');

      $expense = Expense::where('id', $id)->delete();
      if($expense){
        $this->response['status'] = true;
        $this->response['message'] = "Successfully Deleted!";
      }
      return $this->response;
    }
}
