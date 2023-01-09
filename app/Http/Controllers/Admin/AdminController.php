<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Hash;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	 
    public function index()
    {
        return view('admin.dashboard');
    }
	
	public function changePassword(Request $request){

		if($request->ajax()){
			
			$rules=[
				'old_pass' => 'required',
				'password' => 'required|min:8',
				'confirm_password' => 'required|min:8|same:password',
			];
			
			$validator = Validator::make($request->all(), $rules);
 
			if ($validator->fails()) {
				$message = [];
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				} 
				
				return response(array("error"=>false,"message" => $message),403); 
						
			}else{
				
				$checkUser = User::where(['id'=>Auth::user()->id])->first();
				
				if(!Hash::check($request->old_pass, $checkUser->password)){
					
					return response(array('message'=>'Old password does not match. please try again.'),403);
					
				}else{
					
					User::where('id',Auth::user()->id)->update(['password'=>Hash::make($request->password)]);
					
					return response(array('message'=>'Password updated successfully.','reset'=>true),200);
				}

			}
		}
		
		return view('admin.change_password');
	}

}
