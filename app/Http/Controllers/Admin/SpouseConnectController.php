<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpouseConnectController extends Controller
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
            
				if(!empty($request->post('users')) && (count($request->post('users'))>1) && (count($request->post('users')) < 3)){

					$userMain = \App\Models\User::where('id',$request->post('users')[0])->first();

					$userCheckExitsInGroup = \App\Models\User::where('id',$request->post('users')[1])->first();

					if($userMain && $userCheckExitsInGroup && $userMain->gender == $userCheckExitsInGroup->gender){

						return response(array('message'=>'Selected user both gender are same'),403);

					}else{

						if($request->post('type') == 'update'){

							$userExitsInGroup = \App\Models\User::where('parent_id',$request->post('users')[0])->where('added_as','Spouse')->get();
						
							if(!empty($userExitsInGroup) && count($userExitsInGroup)>0){
	
								foreach($userExitsInGroup as $key=>$userVal){
	
									$user = \App\Models\User::where('id',$userVal->id)->first();
									if($user){
										$user->parent_id = null;
										$user->added_as = null;
										$user->save();
									}
									
								}
							}
	
						}
	
						foreach($request->post('users') as $key=>$user){
	
							if($key++){
								$user = \App\Models\User::where('id',$user)->first();
								if($user){
									$user->parent_id = $request->post('users')[0];
									$user->added_as = 'Spouse';
									$user->save();
								}
							}
							
						}
					}
					

					return response(array('message'=>'Spouse connected successfully.', 'reload'=>true),200);

				}else{

					return response(array('message'=>'Please select at least one or not more then one spouse'),403);
				}
                
			}
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();

		$query = \App\Models\User::where([['stage', '>=', '3'],['designation_id', 2],['designation_id', '!=', '14'],['marital_status', '=', 'Married']])

					->where(function ($query1) {
						$query1->where('added_as',null)
							->orWhere('added_as', '=', 'Group');
					})->orderBy('updated_at', 'desc');

		$users = $query->get();

        return view('admin.spouse-connect.add', compact('result','users'));

    }

    public function list(Request $request) {
		
		$columns = \Schema::getColumnListing('users');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		
		$query = \App\Models\User::where([['stage', '>=', '3'],['designation_id', 2],['designation_id', '!=', '14']])

					->where(function ($query1) {
						$query1->where('added_as',null)
							->orWhere('added_as', '=', 'Group');
					})->orderBy('updated_at', 'desc');

		$datas = $query->get();
			
        return view('admin.spouse-connect.list',compact('datas'));

	}

    public function spouseUpdate(Request $request,$id) {
		
		$userLeader = \App\Models\User::where('id', $id)->first();
		$results = \App\Models\User::where([['parent_id', $id],['added_as', 'Spouse']])->first();
		
		$query = \App\Models\User::where([['stage', '>=', '3'],['designation_id', 2],['designation_id', '!=', '14'],['marital_status', '=', 'Married']])

					->where(function ($query1) {
						$query1->where('added_as',null)
							->orWhere('added_as', '=', 'Group');
					})->orderBy('updated_at', 'desc');

		$users = $query->get();
		
        return view('admin.spouse-connect.update',compact('users','results','userLeader'));

	}
}
