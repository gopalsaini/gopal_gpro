<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'users'=>'array|required',
			];

			$validator = \Validator::make($request->all(), $rules);
			
			if ($validator->fails()){
				$message = "";
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				}
				
				return response(array('message'=>$message),403);
				
			} else {
            
				if(!empty($request->post('users')) && count($request->post('users'))>1){

					foreach($request->post('users') as $key=>$user){

						if($key++){
							$user = \App\Models\User::where('id',$user)->first();
							if($user){
								$user->parent_id = $request->post('users')[0];
								$user->added_as = 'Group';
								$user->save();
							}
						}
						
					}

					return response(array('message'=>'Group create successfully.', 'reload'=>true),200);

				}else{

					return response(array('message'=>'Please select at least one group member'),403);
				}
                
			}
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();

		$users = \App\Models\User::where([['id', '!=', '1'],['designation_id', '2'],['name','!=', '']])
									->where('added_as',null)->orderBy('updated_at', 'desc')->get();

        return view('admin.community.add', compact('result','users'));

    }

    public function list(Request $request) {
		
		$columns = \Schema::getColumnListing('users');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		
		$query = \App\Models\User::where([['id', '!=', '1'],['designation_id', 2],['designation_id', '!=', '14']])->orderBy('updated_at', 'desc');


		$datas = $query->get();
			
		
        return view('admin.community.list',compact('datas'));

	}

    public function edit($id) {
		
		$result = \App\Models\Community::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.community.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Community::find($id);
		
		if ($result) {

			\App\Models\Community::where('id', $id)->delete();
			$request->session()->flash('5fernsadminsuccess','Community deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Community::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Community status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	
    public function groupUsersGroupUpdate(Request $request,$id) {
		
		$userLeader = \App\Models\User::where('id', $id)->first();
		$results = \App\Models\User::where([['parent_id', $id],['added_as', 'Group']])->get();

		$users = \App\Models\User::where([['id', '!=', '1'],['designation_id', '2'],['name','!=', '']])
									->where('added_as',null)->orderBy('updated_at', 'desc')->get();

        return view('admin.community.update',compact('users','results','userLeader'));

	}
}
