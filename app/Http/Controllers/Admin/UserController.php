<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UserController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){

			if((int) $request->post('id') > 0) {

				$rules = [
					'id' => 'numeric|required',
					'first_name' => 'required|string',
					'last_name' => 'required|string',
					'phone_code' => 'required',
					'mobile' => 'required|numeric',
					'gender' => 'required|in:1,2',
					'dob' => 'required|date',
					// 'citizenship' => 'required|string',
				];

			} else {

				$rules = [
					'id' => 'numeric|required',
					'name' => 'string|required',
					'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->where(function ($query) use($request) {
						return $query->where('id', '!=', $request->id)->where('deleted_at', NULL);
					})],
					'designation_id'=>'required|exists:designations,id'
				];

			}

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

				$password = \Str::random(10);
				
				if ((int) $request->post('id') > 0) {

					$dob=date('Y-m-d',strtotime($request->post('dob')));
					$date1 = $dob;
					$date2 = date('Y-m-d');
					$diff = abs(strtotime($date2) - strtotime($date1));
					$years = floor($diff / (365*60*60*24));
				
					if ($years < 18) {

						return response(array("error"=>true, 'message'=>'Birth year must be more than 18 years'), 403);

					}

					$data=\App\Models\User::find($request->post('id'));

					$data->salutation = $request->post('salutation');
					$data->name = $request->post('first_name');
					$data->last_name = $request->post('last_name');
					$data->phone_code = $request->post('phone_code');
					$data->mobile = $request->post('mobile');
					$data->gender = $request->post('gender');
					$data->dob = $dob;
					// $data->citizenship = $request->post('citizenship');

				} else {

					$data=new \App\Models\User();
					$data->email = $request->post('email');
					$data->name = $request->post('name');
					$data->reg_type = 'email';
					$data->designation_id = $request->post('designation_id');
					$data->parent_id = null;
					$data->password = \Hash::make($password);


				}

				$data->save();
				
				if ((int) $request->post('id') == 0) {

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->user_id=$data->id;
					$UserHistory->action_id = \Auth::user()->id;
					$UserHistory->action='Your registration for GProCongress II has started!';
					$UserHistory->save();

					$url = '<a href="'.url('profile-update').'">Click here</a>';
					$faq = '<a href="'.url('faq').'">Click here</a>';

					$to = $request->post('email');
					$subject = 'Your registration for GProCongress II has started!';
					
					$msg = '<div>Dear '.$request->post('name').',</div><div><br></div><div>Based on your discussion with '.\Auth::user()->name.' your registration for the GProCongress II has been initiated. Please use this link '.$url.' to edit and complete your application at any time.<br> Your registered email and password are:</div><div><br>Email: '.$to.'<br>Password: '.$password.'<br></div><div>To find out more about the criteria to attend the Congress, '.$faq.'</div><div><br></div><div>'.$request->post('name').', We are here to help! To talk with one of our team members, simply respond to this email.</div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div><br></div><div>Warmly,</div><div>GProCongress II Team</div>';


					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);

					return response(array('message'=>'User added successfully.', 'reset'=>true), 200);

				} else {

					return response(array('message'=>'User updated successfully.', 'reset'=>false), 200);
				}

			}

			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		$designations = \App\Models\Designation::where('slug', '!=', 'admin')->get();
		
		$result = array();
        return view('admin.user.add', compact('result', 'designations'));

    }

    public function list(Request $request, $designation) {

        return view('admin.user.list', compact('designation'));
	}

	public function stageAll(Request $request, $type) {
 
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['parent_id', NULL], ['added_as', NULL]])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['parent_id', NULL], ['added_as', NULL]])->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();
			
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name.' '.$data->last_name;
		    })
			->addColumn('user_name', function($data){

				if(\App\Helpers\commonHelper::checkGroupUsers($data->email)){
					return '<a href="javascript:void(0)" class="group-user-list" data-email="'.$data->email.'"></a> '.$data->email ;
				} else {
					return $data->email;
				}

		    })

			->addColumn('stage0', function($data){
				if($data->stage == 0){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 0){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}  
			})

			->addColumn('stage1', function($data){
				if($data->stage == 1){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 1){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage2', function($data){
				if($data->stage == 2){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 2){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage3', function($data){
				if($data->stage == 3){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 3){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage4', function($data){
				if($data->stage == 4){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 4){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage5', function($data){
				if($data->stage == 5){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 5){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })
 

			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this user ?'";
				
				if (\Auth::user()->designation_id == '1' ) {

					return '
						<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						<a href="'.route('admin.user.edit', ['id' => $data->id] ).'" title="Edit User" class="btn btn-sm btn-success px-3 m-1 text-white "><i class="fas fa-pencil-alt"></i></a>
					';
				}else{
					
					return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></div>';

				}
			})

			

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		
        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$stageno = 'all';
        return view('admin.user.stage.stage-all', compact('type', 'setting', 'stageno'));

	}

    public function stageZero(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '0']])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '0']]);
			
			
			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();
			
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			
			->addColumn('name', function($data){
				return $data->name.' '.$data->last_name;
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('profile', function($data){
				if ($data->profile_status == 'Review') {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
				}
		    })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })


			->addColumn('action', function($data){
				
				$msg = "' Are you sure you want to delete this user ??'";

				if (\Auth::user()->designation_id == '11') {
					return '<div style="display:flex"><a class="btn btn-sm btn-dark px-3 m-1 text-white sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope"></i></a>
					<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></a>
					</div>';

				}elseif(\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '12'){

					return '<div style="display:flex"><a class="btn btn-sm btn-dark px-3 m-1 text-white sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></a>
						<a href="'.route('admin.user.archiveUserDelete', ['id' => $data->id] ).'" title="user delete" class="btn btn-sm btn-danger px-3 m-1 text-white" onclick="return confirm('.$msg.')"><i class="fas fa-trash"></i></a></a>
						</div>';

				}
			})

			->addColumn('created_at', function($data){
				return date('Y-m-d h:i', strtotime($data->created_at));
		    })

			->addColumn('updated_at', function($data){
				return date('Y-m-d h:i', strtotime($data->updated_at));
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$stageno = '0';
        return view('admin.user.stage.stage-zero', compact('type', 'setting', 'stageno'));

	}

    public function stageOne(Request $request, $type) {
		
		if ($request->ajax()) { 

			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['stage', '=', '1'], ['profile_status', $request->input('status')]])
						->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						})->orderBy('updated_at', 'desc');

						
			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['stage', '=', '1'], ['profile_status', $request->input('status')]])
						->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						});

						
			
			if (request()->has('email')) {
				
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name.' '.$data->last_name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('profile', function($data){
				if ($data->profile_update) {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
				}
		    })

			// ->addColumn('status', function($data){
			// 	if($data->status=='1'){ 
			// 		$checked = "checked";
			// 	}else{
			// 		$checked = " ";
			// 	}

			// 	return '<div class="media-body icon-state switch-outline">
			// 				<label class="switch">
			// 					<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
			// 				</label>
			// 			</div>';
		    // })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })

			->addColumn('action', function($data){

				if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '12') && $data->profile_status == 'Review' ) {
					
					if (\Auth::user()->designation_id == '11' ) {

						return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';

					}
					
						return '<div style="display:flex">
						<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
						<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Decline" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a>
						<a href="javascript:void(0)" title="Waiting Profile" data-id="'.$data->id.'" data-status="Waiting" class="btn btn-sm btn-warning px-3 m-1 text-white profile-status"><i class="fas fa-pause"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
				} else if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '12' || \Auth::user()->designation_id == '11') && $data->profile_status == 'Waiting' ) {
					
					if (\Auth::user()->designation_id == '11') {
						
						return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';

					}
					return '<div style="display:flex">
					<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
					<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Decline" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a>
					<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
				} else if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '12' || \Auth::user()->designation_id == '11') && $data->profile_status == 'Rejected') {
					
					return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
				}
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$offers = \App\Models\Offer::get();
		$stageno = 1;
        return view('admin.user.stage.stage-one', compact('type', 'setting', 'offers', 'stageno'));

	}

	public function stageTwo(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '2']])
			->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			})->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '2']])
			->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			});

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();
			
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('amount', function($data){
				return '$'.number_format($data->amount, 2) ?? '-';
		    })


			->addColumn('amount_in_process', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalAmountInProcess($data->id, true);
		    })
			
			->addColumn('accepted_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalAcceptedAmount($data->id, true);
			})

			->addColumn('rejected_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalRejectedAmount($data->id, true);
		    })

			->addColumn('pending_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalPendingAmount($data->id, true);
		    })

			->addColumn('payment_status', function($data){
				if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}
		    })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })

			->addColumn('action', function($data){
				
				if (\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '13') {
					return '<div style="display:flex"><a class="btn btn-sm btn-dark m-1 sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope" style="color:#fff"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1" ><i class="fas fa-eye" style="color:#fff"></i></a>
						<a href="'.route('admin.user.payment.history', ['id' => $data->id] ).'" title="User payment history" class="btn btn-sm btn-warning m-1"><i class="fas fa-list" style="color:#fff"></i></a></div>
						<a href="#" data-id="'.$data->id.'" title="User Cash payment " class="btn btn-sm btn-warning m-1 cashPayment">Cash Payment</a></div>';
				}
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1 || \Auth::user()->designation_id == 13){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 2;
			return view('admin.user.stage.stage-two', compact('type', 'setting', 'stageno'));

		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}
        

	}

	public function stageThree(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('TravelInfo')->where([['designation_id', $designation_id], ['stage', '=', '3']])->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			})->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('TravelInfo')->where([['designation_id', $designation_id], ['stage', '=', '3']])->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			});

			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('user_name', function($data){
				return $data->name;
		    })

			->addColumn('email', function($data){
				return $data->email;
			})

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile;
		    })

			
			->addColumn('remark', function($data){

				if($data->TravelInfo && $data->TravelInfo->remark){
					return '<button type="button" class="btn btn-sm btn ViewRemark" data-remark="'.$data->TravelInfo->remark.'"> View </button>';
				}else{
					return '-';
				}
				
		    })

			->addColumn('user_status', function($data){
				if ($data->TravelInfo) {
					if ($data->TravelInfo->user_status == '1') {
						return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
					} else if ($data->TravelInfo->user_status == '0') {
						return '<div class="span badge rounded-pill pill-badge-danger">Decline</div>';
					} else if ($data->TravelInfo->user_status === null) {
						return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
					}
				}else {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
		    })

			->addColumn('admin_status', function($data){

				$draft_file = '';
				$final_file = '';

				if ($data->TravelInfo && $data->TravelInfo->user_status == '1') {

					if ($data->TravelInfo->admin_status == '1') {

						if ($data->TravelInfo->final_file != '') {

							$final_file =  '<a href="'.asset('uploads/file/'.$data->TravelInfo->final_file).'" target="_blank" class="btn btn-sm btn-outline-success m-1">View File</a>';
						}

						return '<div class="badge rounded-pill pill-badge-success">Approved</div>'.$final_file;
					} else if ($data->TravelInfo->admin_status == '0') {
						return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';
					} else if ($data->TravelInfo->status === null) {

						if ($data->TravelInfo->user_status == '1' && $data->TravelInfo->draft_file != '') {


							$draft_file =  '<a href="'.asset('uploads/file/'.$data->TravelInfo->draft_file).'" target="_blank" class="btn btn-sm btn-outline-success m-1">View File</a>';
						}
						return '<div style="display:flex"><a data-id="'.$data->TravelInfo->id.'" data-type="1" title="Travel Info Approve" class="btn btn-sm btn-outline-success m-1 sendFinalLetter">Approve</a>'.$draft_file.'</div>';
					}

				} else {

					if ($data->TravelInfo && $data->TravelInfo->remark) {

						return '<div style="display:flex"><a data-id="'.$data->TravelInfo->id.'" title="Send Draft letter" class="btn btn-sm btn-outline-success m-1 sendDraftLetter">Draft</a>';
					
					}

					return '<a class="btn btn-sm btn-dark sendEmail" data-id="'.$data->id.'" ><span style="color:#fff">Primarily Letter Issue</span></a>';
				}

		    })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })

			->addColumn('action', function($data){
				
				if (\Auth::user()->designation_id == '1') {
					return '<div style="display:flex"><a href="'.route('admin.user.travel.info', ['id' => $data->id] ).'" title="User travel info" class="btn btn-sm btn-warning m-1"><i class="fas fa-plane"></i></a>
						<a href="'.route('admin.user.payment.history', ['id' => $data->id] ).'" title="User payment history" class="btn btn-sm btn-primary m-1"><i class="fas fa-money"></i></a><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 3;
			return view('admin.user.stage.stage-three', compact('type', 'setting', 'stageno'));
	
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}

	public function stageFour(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '4']])->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '4']]);
			
			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();


			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('user_name', function($data){
				return $data->name;
		    })

			->addColumn('day', function($data){
				if (count($data->SessionInfo) > 0) {
					$day = '';
					foreach ($data->SessionInfo as $dayValue) {
						
						$sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
						if($sessionInfo){
							$day .= 'Date :'.$dayValue->day.', ';
							$day .= 'Name :'.$sessionInfo->session_name.', ';
							$day .= 'Session Join :'.$dayValue->session.', ';
							$day .= 'Start Time :'.$sessionInfo->start_time.', ';
							$day .= 'End Time :'.$sessionInfo->end_time;
							$day .= '<br>';
						}
						
					}
					return $day;
				}else {
					return '-';
				}
		    })

			->addColumn('user_status', function($data){
				if (count($data->SessionInfo) > 0) {
					if ($data->SessionInfo[0]->admin_status == '1') {
						return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
					} else if ($data->SessionInfo[0]->user_status == '0') {
						return '<div class="span badge rounded-pill pill-badge-danger">Reject</div>';
					} else if ($data->SessionInfo[0]->user_status === null) {
						return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
					}
				}else {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
		    })

			->addColumn('admin_status', function($data){

				if (count($data->SessionInfo) > 0) {

					if ($data->SessionInfo[0]->admin_status == '1') {
						return '<div class="badge rounded-pill pill-badge-success">Approved</div>';
					} else if ($data->SessionInfo[0]->admin_status == '0') {
						return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';
					} else if ($data->SessionInfo[0]->status === null) {
						return '<div style="display:flex"><a data-id="'.$data->id.'" data-type="1" title="Session Info Approve" class="btn btn-sm btn-outline-success m-1 -change">Approve</a>
						<a data-id="'.$data->id.'" data-type="0" title="Session Info Reject" class="btn btn-sm btn-outline-danger m-1 -change">Reject</a></div>';
					}
					
				} else {

					return '<a class="btn btn-sm btn-dark px-3 sendEmail" data-id="'.$data->id.'"><span style="color:#fff">Send Mail</span></a>';
				}

		    })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })
			->addColumn('action', function($data){
				
				if (\Auth::user()->designation_id == '1') {
					return '<a href="'.route('admin.user.session.info', ['id' => $data->id] ).'" title="User session info" class="btn btn-sm btn-warning px-3"><i class="fas fa-list"></i></a><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 4;
			return view('admin.user.stage.stage-four', compact('type', 'setting', 'stageno'));
		
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}

	public function stageFive(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '5']])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '5']]);
			
			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();


			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('user_name', function($data){
				return $data->name;
		    })

			->addColumn('profile', function($data){
				if ($data->profile_update == '1') {
					return '<div class="span badge rounded-pill pill-badge-success">Updated</div>';
				} else if ($data->user_status == '0') {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
		    })

			->addColumn('payment', function($data){
				if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}
		    })

			->addColumn('travel_info', function($data){
				if ($data->TravelInfo) {
					if ($data->TravelInfo->user_status == '1') {
						return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
					} else if ($data->TravelInfo->user_status == '0') {
						return '<div class="span badge rounded-pill pill-badge-danger">Reject</div>';
					} else if ($data->TravelInfo->user_status === null) {
						return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
					}
				}else {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
		    })

			->addColumn('session_info', function($data){
				if (count($data->SessionInfo) > 0) {
					if ($data->SessionInfo[0]->admin_status == '1') {
						return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
					} else if ($data->SessionInfo[0]->user_status == '0') {
						return '<div class="span badge rounded-pill pill-badge-danger">Reject</div>';
					} else if ($data->SessionInfo[0]->user_status === null) {
						return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
					}
				}else {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
		    })

			->addColumn('user_type', function($data){
				
				if($data->parent_id != Null){

					if($data->added_as == 'Group'){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}elseif($data->added_as == 'Spouse'){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
						
					}

				}else {

					$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
					$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
					if($groupName){

						return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
						
					}else if($spouseName){

						return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

					}else{

						return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
					}
						

				}
				
		    })

			->addColumn('group_owner_name', function($data){
				
				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
				
				if($data->parent_id != Null && $data->added_as == 'Group'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
					
				}else if(count($groupName) > 0) {

					return ucfirst($data->name.' '.$data->last_name);

				}else{
					return 'N/A';
				}
				
		    })

			->addColumn('spouse_name', function($data){
				
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
				
				if($data->parent_id != Null && $data->added_as == 'Spouse'){

					return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

				}else if($spouseName) {

					return ucfirst($spouseName->name.' '.$spouseName->last_name);

				}else{

					return 'N/A';
				}
				
		    })
			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this user ?'";

				if (\Auth::user()->designation_id == '1') {
					return '<a href="'.route('admin.user.details', ['id' => $data->id] ).'" title="View user details" class="btn btn-sm btn-primary px-3" ><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 5;
			return view('admin.user.stage.stage-five', compact('type', 'setting', 'stageno'));
			
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}

    public function edit($id) {
		
		$result = \App\Models\User::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		$designations = \App\Models\Designation::where('slug', '!=', 'admin')->get();
		$countries = \App\Models\Country::get();

		return view('admin.user.edit', compact('result', 'designations', 'countries'));

	}

    public function delete(Request $request, $id) {

		
		$result = \App\Models\User::find($id);
		
		if ($result) {

			\App\Models\User::where('id', $id)->delete();
			\App\Models\User::where('parent_id', $id)->delete();
			
			$request->session()->flash('5fernsadminsuccess','User deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->status_change_at = date('Y-m-d H:i:s');
			$result->amount = $request->post('amount');
			$result->payment_status = 0;

			$to = $result->email;
			if ((int)$request->post('status') === 1) {
				$result->stage = 2;

				
				$subject = 'Profile Approved';
				$msg = 'Your profile has been approved successfully, please pay this amount '.$request->post('amount').' and your offer code is '.$request->post('offer_code');
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

				$resultSpouse = \App\Models\User::where('parent_id',$request->post('id'))->get();
				if(!empty($resultSpouse) && count($resultSpouse) >0){

					foreach($resultSpouse as $val){

						$resultSpouseFirst = \App\Models\User::find($val->id);

						$resultSpouseFirst->status = $request->post('status');
						$resultSpouseFirst->status_change_at = date('Y-m-d H:i:s');
						$resultSpouseFirst->amount = $request->post('amount');
						$resultSpouseFirst->payment_status = 0;

						$to = $resultSpouseFirst->email;

						$resultSpouseFirst->stage = 2;
						$resultSpouseFirst->save();

						$subject = 'Profile Approved';
						$msg = 'Your profile has been approved successfully, please pay this amount '.$request->post('amount').' and your offer code is '.$request->post('offer_code');
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($resultSpouseFirst->mobile);
					}
					
				}


			} else if ((int)$request->post('status') === 0) {
				$subject = 'Profile Rejected';
				$msg = 'Your profile has been rejected';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);
			}

			$result->save();

			return response(array('message'=>'User status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendProfileUpdateReminder(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {

			$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';
			
			$to = $result->email;
			$name= $result->name.' '.$result->last_name;
			$subject = 'Friendly reminder: Your GProCongress II application needs to be completed';
			$msg = '<div>Dear '.$name.',</div><div><br></div><div>We have not received your completed application to attend the GProCongress II. Please use this link '.$url.' to edit and complete your application at any time. We recommend timely completion to secure a spot.</div><div><br></div><div>As always, to talk with one of our team members, simply respond to this email.</div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div>Warmly,</div><div>GProCongress II Team</div>';
			
			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Profile update reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendPaymentReminder(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {

			$name = $result->name.' '.$result->last_name;

			\App\Helpers\commonHelper::sendPaymentReminderMailSend($request->post('id'),$result->email,$name);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Payment pending reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function stageSetting(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'stage_zero'=>'required|in:0,1',
				'stage_one'=>'required|in:0,1',
				'stage_two'=>'required|in:0,1',
				'stage_three'=>'required|in:0,1',
				'stage_four'=>'required|in:0,1',
				'stage_five'=>'required|in:0,1',
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

                $data=\App\Models\StageSetting::find($request->post('id'));

				if (!$data) {
					return response(array('message'=>'Data not found.'),403);
				}
                
                $data->stage_zero = $request->post('stage_zero');
                $data->stage_one = $request->post('stage_one');
                $data->stage_two = $request->post('stage_two');
                $data->stage_three = $request->post('stage_three');
                $data->stage_four = $request->post('stage_four');
                $data->stage_five = $request->post('stage_five');
                $data->save();
                
            	return response(array('message'=>'Stage updated successfully.'),200);

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$results = \App\Models\StageSetting::With('Designation')->get();
        return view('admin.user.stage-setting', compact('results'));

    }

	public function userProfile(Request $request, $id) {

		$result = \App\Models\User::with('TravelInfo')->where([['id', '=', $id]])->first();
		
		if (!$result) {

			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.profile', compact('id', 'result'));

	}

	public function travelInfo(Request $request, $id) {

		$result = \App\Models\User::with('TravelInfo')->where([['id', '=', $id]])->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.travel-info', compact('id', 'result'));

	}

	public function sessionInfo(Request $request, $id) {

		$result = \App\Models\User::with('SessionInfo')->where([['id', '=', $id]])->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.session-info', compact('id', 'result'));

	}

	public function paymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '1')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '1')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('user_name', function($data){
				return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		    })
			
			->addColumn('created_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->created_at));
		    })

			
			->addColumn('transaction', function($data){
				return $data->order_id;
		    })

			->addColumn('utr', function($data){
				return $data->bank_transaction_id;
		    })

			->addColumn('bank', function($data){
				return $data->bank." Transfer";
		    })

			->addColumn('type', function($data){
				
				return 'Credit';
				
		    })


			->addColumn('mode', function($data){
				return $data->method;
		    })

			->addColumn('amount', function($data){
				return '$'.$data->amount;
		    })

			->addColumn('payment_status', function($data){

				if($data->status == '0'){

					return "Pending";

				}elseif($data->status == '1'){

					return "Accepted";
					
				}else{
					return "decline";
				}
				
		    })

			->addColumn('updated_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->updated_at));
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function sponsoredPaymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '2')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '2')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('created_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->created_at));
		    })

			->addColumn('user_name', function($data){
				return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		    })
			
			->addColumn('transaction', function($data){
				return $data->transaction_id;
		    })

			->addColumn('utr', function($data){
				return $data->bank_transaction_id;
		    })

			->addColumn('bank', function($data){
				return $data->bank." Transfer";
		    })

			->addColumn('type', function($data){
				
				return 'Credit';
				
		    })


			->addColumn('mode', function($data){
				return $data->method;
		    })

			->addColumn('amount', function($data){
				return $data->amount;
		    })

			->addColumn('payment_status', function($data){

				if($data->status == '0'){

					return "Pending";

				}elseif($data->status == '1'){

					return "Accepted";
					
				}else{
					return "decline";
				}
				
		    })

			->addColumn('updated_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->updated_at));
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function donatePaymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '3')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '3')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('created_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->created_at));
		    })

			->addColumn('user_name', function($data){
				return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		    })
			
			->addColumn('transaction', function($data){
				return $data->transaction_id;
		    })

			->addColumn('utr', function($data){
				return $data->bank_transaction_id;
		    })

			->addColumn('bank', function($data){
				return $data->bank." Transfer";
		    })

			->addColumn('type', function($data){
				
				return 'Credit';
				
		    })


			->addColumn('mode', function($data){
				return $data->method;
		    })

			->addColumn('amount', function($data){
				return $data->amount;
		    })

			->addColumn('payment_status', function($data){

				if($data->status == '0'){

					return "Pending";

				}elseif($data->status == '1'){

					return "Accepted";
					
				}else{
					return "decline";
				}
				
		    })

			->addColumn('updated_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->updated_at));
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function travelInfoStatus(Request $request) {
		
		$result = \App\Models\TravelInfo::find($request->post('id'));

		if ($result) {
			$result->admin_status = $request->post('status');
			$result->save();

			$user = \App\Models\User::with('TravelInfo')->find($result->user_id);
			
			$pdfData = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$result->user_id)->first();

			$to = $user->email;
			$pdf = \PDF::loadView('email_templates.travel_info', $pdfData->toArray());

			if ((int)$request->post('status') === 1) {

				
				$user->stage = 4;
				$user->save();

				$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
			
				if($resultSpouse){

					$resultSpouse->stage = 4;
					$resultSpouse->save();

				}

				$subject = 'Travel Info Approved';
				$msg = 'Your travel info has been approved successfully';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
			
				// \App\Helpers\commonHelper::sendSMS($user->mobile);

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Travel Info Approved',\Auth::user()->id);
				
				$subject = 'Session information ';
				$msg = 'Your Travel Information has been approved successfully, Please session information can be updated now';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Session information',\Auth::user()->id);
				

			} else if ((int)$request->post('status') === 0) {

				$subject = 'Travel Info Rejected';
				$msg = 'Your travel info has been rejected';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
				// \App\Helpers\commonHelper::sendSMS($user->mobile);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

			}

			return response(array('message'=>'Travel info status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendTravelInfoReminder(Request $request) {
		
		$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$request->post('id'))->first();
		
		if ($result) {
			$to = $result->email;
			
			if($result->language == 'sp'){

				$subject = "Por favor, envíe su información de viaje.";
				$msg = '<p>Dear '.$result->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp; &nbsp;&nbsp;</p>';
			
			}elseif($result->language == 'fr'){
			
				$subject = "Veuillez soumettre vos informations de voyage.";
				$msg = "<p>Cher '.$result->name.',&nbsp;</p><p>Nous sommes ravis de vous voir au GProCongrès à Panama City, au Panama !</p><p><br></p><p>Pour aider les délégués à obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</p><p><br></p><p>Veuillez répondre à cet e-mail avec vos informations de vol.&nbsp; Dès réception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons reçues sont correctes.&nbsp;</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>";
	
			}elseif($result->language == 'pt'){
			
				$subject = "Por favor submeta sua informação de viagem";
				$msg = '<p>Prezado '.$result->name.',&nbsp;</p><p><br></p><p>Nós estamos emocionados em ver você no CongressoGPro na Cidade de Panamá, Panamá!</p><p><br></p><p>Para ajudar os delegados na obtenção de vistos, nós estamos pedindo que submetam a nós sua informação de viagem.&nbsp;</p><p><br></p><p>Por favor responda este e-mail com informações do seu voo. Depois de recebermos, iremos lhe enviar um e-mail confirmando que a informação que recebemos é correta.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro&nbsp; &nbsp; &nbsp;&nbsp;</p>';
			
			}else{
			
				$subject = 'Please submit your travel information.';
				$msg = '<p>Dear '.$result->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
									
			}
			// echo "<pre>"; print_r($result->toArray());die;
			
			$pdf = \PDF::loadView('email_templates.travel_info', $result->toArray());
			$pdf->setPaper('L');
			$pdf->output();
			$canvas = $pdf->getDomPDF()->getCanvas();
			
			$height = $canvas->get_height();
			$width = $canvas->get_width();
			$canvas->set_opacity(.2,"Multiply");
			$canvas->page_text($width/5, $height/2, 'Draft', null,
			70, array(0,0,0),2,2,-30);

			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Travel info reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sessionInfoStatus(Request $request) {
		
		$result = \App\Models\SessionInfo::where('user_id', $request->post('id'))->get();

		if (count($result) > 0) {

			\App\Models\SessionInfo::where('user_id', $request->post('id'))->update(['admin_status' => $request->post('status'),'user_status' => $request->post('status')]);
			$user = \App\Models\User::find($request->post('id'));
			$to = $user->email;
			if ((int)$request->post('status') === 1) {
				
				
				$user->stage = 5;
				$user->qrcode = \QrCode::size(300)->generate(url('/'));
				$user->save();

				$subject = 'Session Info Approved';
				$msg = 'Your session info has been approved successfully';
				$msg = '<br>';

				$data = \App\Models\User::with('SessionInfo')->where([['stage', '=', '5'], ['id', '=', $request->post('id')]])->first();
				
				foreach ($data->SessionInfo as $dayValue) {
					
					$sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
					if($sessionInfo){
						$msg .= 'Date :'.$dayValue->day.', ';
						$msg .= 'Name :'.$sessionInfo->session_name.', ';
						$msg .= 'Session Join :'.$dayValue->session.', ';
						$msg .= 'Start Time :'.$sessionInfo->start_time.', ';
						$msg .= 'End Time :'.$sessionInfo->end_time;
						$msg .= '<br>';
					}
					
				}
				
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);
				// \App\Helpers\commonHelper::sendSMS($user->mobile);
			} else if ((int)$request->post('status') === 0) {

				$subject = 'Session Info Decline';
				$msg = 'Your session info has been decline';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($user->mobile);
			}

			return response(array('message'=>'Session info status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendSessionInfoReminder(Request $request) {
		
		$result = \App\Models\User::with('SessionInfo')->find($request->post('id'));

		if ($result) {
			$to = $result->email;
			$subject = 'Verify Session Info';
			$msg = 'Please verify your session information';

			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Session info reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function userDetails(Request $request, $id) {
		
		$result = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['id', '=', $id], ['stage', '=', '5']])->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.user-details', compact('id', 'result'));

	}
	

	public function groupUsersList(Request $request) {

		if ($request->ajax()) {

			$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
			$results = \App\Models\User::where([['parent_id', $id]])->get();

			$html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"> <thead> <tr> <th class="text-center">'. \Lang::get('admin.id') .'</th> <th class="text-center">'. \Lang::get('admin.addedas') .'</th> <th class="text-center">'. \Lang::get('admin.user') .'</th> <th class="text-center">'. \Lang::get('admin.stage') .' 0 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 1 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 2 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 3 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 4 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 5 </th> <th class="text-center">'. \Lang::get('admin.action') .'</th> </tr> </thead><tbody>';
			
			if (count($results) > 0) {
				foreach ($results as $key=>$result) {

					$spouse = \App\Models\User::where([['parent_id', $result->id]])->first();

					$key += 1;
					$html .= '<tr>';
					$html .= '<td class="text-center">'.$key.'.</td>';

					$html .= '<td class="text-center">'.$result->added_as;
					$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->added_as.'</p>' : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->email;
					$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->email.'</p>' : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('0', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('0', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('1', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('1', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('2', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('2', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('3', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('3', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('4', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('4', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('5', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('5', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center"><a href="'.route('admin.user.profile', ['id' => $result->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>';
					$html .= $spouse ? '<hr><a href="'.route('admin.user.profile', ['id' => $spouse->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>' : '';
					$html .= '</td>';

					$html .= '</tr>';
				}
			} else {
				$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
			}
			$html .= '</tbody></table>';

			return response()->json(array('html'=>$html));
			
        }

	}
	
	public function ProfileApproved(Request $request, $id) {
		
		$result = \App\Models\User::where('id', $id)->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        $result->profile_update = '1';
        $result->profile_updated_at = date('Y-m-d');
        $result->save();

		$request->session()->flash('error','Profile Approved Successfully.');
		return redirect()->back();

	}

	public function profileReject(Request $request, $id) {
		
		$result = \App\Models\User::where('id', $id)->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        $result->profile_update = '2';
        $result->profile_updated_at = date('Y-m-d');
        $result->save();

		$request->session()->flash('error','Profile Reject Successfully.');
		return redirect()->back();

	}

	public function profileStatus(Request $request) {
		
		
		$result = \App\Models\User::find($request->post('user_id'));
		
		if ($result) {

			$result->profile_status = $request->post('status');
			$result->remark = $request->post('remark');
			$to = $result->email;

			if ($request->post('status') == 'Approved') {

				$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$result->id)->first();
				
				if($resultSpouse){
					
					if($resultSpouse->profile_status == 'Review'){

						if($request->post('room_type') == 'Yes' && $request->post('category') != ''){
							$result->room = $request->post('category');
						}

						$result->change_room_type = $request->post('room_type');
						$result->upgrade_category = $request->post('category');
						$result->early_bird = $request->post('early_bird');
						$result->offer_id = $request->post('offer_id');
						$result->amount = $request->post('amount');
						$result->payment_country = $request->post('citizenship');
						$result->cash_payment_option = $request->post('cash_payment');

						$result->stage = '2';
						$result->profile_update = '1';
						$name= $result->name.' '.$result->last_name;

						if($result->language == 'sp'){

							$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>¡Nos da gran alegría confirmar que su solicitud para asistir al GProCongress II ha sido aceptada! Esperamos verle en Ciudad de Panamá en noviembre de 2023, Dios mediante.</p><p><br></p><p><br></p><p>Mientras usted se prepara para venir, por favor, únasenos en oración por los demás participantes.&nbsp;</p><p><br></p><p><br></p><p>¿Todavía tiene preguntas o necesita ayuda? Responda a este correo electrónico y nuestro equipo se pondrá en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($result->language == 'fr'){
						
							$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>C’est avec une grande joie que nous confirmons l’acceptation de votre candidature pour assister au GProCongrès II ! Nous avons hâte de vous voir à Panama City en novembre 2023, si le Seigneur le veut.&nbsp;</p><p><br></p><p>Pendant que vous vous préparez, joignez-vous à nous pour prier pour les autres participants.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
						}elseif($result->language == 'pt'){
						
							$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
							$msg = '<p>Prezado '.$name.',</p><p><br></p><p>É para nós um grande prazer confirmar a aceitação do seu pedido de participar no II CongressoGPro. Nós esperamos lhe ver na Cidade de Panamá em Novembro de 2023, se o Senhor permitir.</p><p><br></p><p>A medida que se prepara, por favor junte-se a nós em oração pelos outros participantes.</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assistência? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Congratulations, '.$name.', your application has been approved!';
							$msg = '<p>Dear '.$name.',</p><p><br></p><p>It gives us great joy to confirm the acceptance of your application to attend the GProCongress II! We look forward to seeing you in Panama City in November 2023, the Lord willing.</p><p><br></p><p>As you prepare, please join us in praying for the other attendees.</p><p><br></p><p>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';	
						
						}

						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($result->mobile);

						$resultSpouse->profile_status = $request->post('status');
						$resultSpouse->stage = 2;
						$resultSpouse->save();

						$name= $resultSpouse->name.' '.$resultSpouse->last_name;
						
						if($resultSpouse->language == 'sp'){

							$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>¡Nos da gran alegría confirmar que su solicitud para asistir al GProCongress II ha sido aceptada! Esperamos verle en Ciudad de Panamá en noviembre de 2023, Dios mediante.</p><p><br></p><p><br></p><p>Mientras usted se prepara para venir, por favor, únasenos en oración por los demás participantes.&nbsp;</p><p><br></p><p><br></p><p>¿Todavía tiene preguntas o necesita ayuda? Responda a este correo electrónico y nuestro equipo se pondrá en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($resultSpouse->language == 'fr'){
						
							$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>C’est avec une grande joie que nous confirmons l’acceptation de votre candidature pour assister au GProCongrès II ! Nous avons hâte de vous voir à Panama City en novembre 2023, si le Seigneur le veut.&nbsp;</p><p><br></p><p>Pendant que vous vous préparez, joignez-vous à nous pour prier pour les autres participants.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
						}elseif($resultSpouse->language == 'pt'){
						
							$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
							$msg = '<p>Prezado '.$name.',</p><p><br></p><p>É para nós um grande prazer confirmar a aceitação do seu pedido de participar no II CongressoGPro. Nós esperamos lhe ver na Cidade de Panamá em Novembro de 2023, se o Senhor permitir.</p><p><br></p><p>A medida que se prepara, por favor junte-se a nós em oração pelos outros participantes.</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assistência? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Congratulations, '.$name.', your application has been approved!';
							$msg = '<p>Dear '.$name.',</p><p><br></p><p>It gives us great joy to confirm the acceptance of your application to attend the GProCongress II! We look forward to seeing you in Panama City in November 2023, the Lord willing.</p><p><br></p><p>As you prepare, please join us in praying for the other attendees.</p><p><br></p><p>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';	
						
						}

						$to = $resultSpouse->email;
						
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

						// \App\Helpers\commonHelper::sendSMS($resultSpouse->mobile);

					}else{

						return response(array('error'=>false, 'reload'=>true, 'message'=>'Spouse profile pending'), 403);
		
					}
					
				}else{

					if($request->post('room_type') == 'Yes'  && $request->post('category') != ''){
						$result->room = $request->post('category');
					}
					$result->change_room_type = $request->post('room_type');

					$result->upgrade_category = $request->post('category');
					$result->early_bird = $request->post('early_bird');
					$result->offer_id = $request->post('offer_id');
					$result->amount = $request->post('amount');
					$result->payment_country = $request->post('citizenship');
					$result->cash_payment_option = $request->post('cash_payment');

					$result->stage = '2';
					$result->profile_update = '1';

					$name= $result->name.' '.$result->last_name;
					$subject = 'Congratulations, '.$name.', your application has been approved!';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>It gives us great joy to confirm the acceptance of your application to attend the GProCongress II! We look forward to seeing you in Panama City in November 2023, the Lord willing.</p><p><br></p><p>As you prepare, please join us in praying for the other attendees.</p><p><br></p><p>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

					// \App\Helpers\commonHelper::sendSMS($result->mobile);
				}

				

			}else if ($request->post('status') == 'Decline') {

				$result->profile_status = 'Rejected';

				$faq = '<a href="'.url('faq').'">Click here</a>';
				
				$name= $result->name.' '.$result->last_name;
				
				if($result->language == 'sp'){

					$url = '<a href="'.url('profile-update').'">clic aquí</a>';
				
					$subject = "Estado de su Solicitud para el GProCongress II";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>Gracias por registrarse para participar del GProCongress II.</p><p><br></p><p>Hemos evaluado muchas aplicaciones con varios nivels de participación en la capacitación de pastores, pero lamentablemente sentimos informale que su solicitud ha sido rechazada en esta ocación.&nbsp;</p><p><br></p><p><br></p><p>Sin embargo, esto no significa el fin de nuestra relación.&nbsp;</p><p><br></p><p>Por favor, manténgase conectado a la comunidad GProCommission haciendo : '.$url.'. Recibirá aliento continuo, ideas, apoyo en oración y mucho más mientras usted forma líderes pastorales.</p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->language == 'fr'){
				
					$url = '<a href="'.url('profile-update').'">cliquant ici</a>';
				
					$subject = "Statut de votre demande GProCongrès II";
					$msg = '<p>Cher '.$name.',</p><p><br></p><p><br></p><p>Merci d’avoir postulé pour assister au GProCongrès II.</p><p>Nous avons évalué de nombreuses candidatures avec différents niveaux d’implication de la formation des pasteurs, mais nous avons malheureusement le regret de vous informer que votre candidature a été refusée, cette fois-ci.&nbsp;&nbsp;</p><p><br></p><p>Cependant, ce n’est pas la fin de notre relation.&nbsp;</p><p>Veuillez rester connecté à la communauté GProCommission en : '.$url.'. Vous recevrez des encouragements continus, des idées, un soutien à la prière et autres alors que vous préparez les responsables pastoraux.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->language == 'pt'){
				
					$url = '<a href="'.url('profile-update').'">aqui</a>';
				
					$subject = "Estado do seu pedido para o II CongressoGPro";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pelo seu pedido para participar no II CongressoGPro.</p><p>Nós avaliamos muitos pedidos com vários níveis de envolvimento no treinamento pastoral, mas infelizmente lamentamos informar que o seu pedido foi declinado esta vez.&nbsp;</p><p><br></p><p>Contudo, este não é o fim do nosso relacionamento.</p><p>&nbsp;</p><p>Por favor se mantenha conectado com a nossa ComunidadeGPro clicando : '.$url.'. Você continuará recebendo encorajamento contínuo, ideias, suporte em oração e muito mais, à medida que prepara os líderes pastorais.</p><p><br></p><p>Ainda tem perguntas? Simplesmente responda este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$url = '<a href="'.url('profile-update').'">Click here</a>';
				
					$subject = 'Your GProCongress II application status';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for applying to attend the GProCongress II.</p><p>We have evaluated many applications with various levels of pastor training involvement, but sadly regret to inform you that your application has been declined, this time.&nbsp;</p><p><br></p><p>However, this is not the end of our relationship.&nbsp;</p><p>Please stay connected to the GProCommission community by : '.$url.'. You will receive ongoing encouragement, ideas, prayer support, and more as you prepare pastoral leaders.&nbsp;</p><p><br></p><p>Do you still have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
					
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

			}else if ($request->post('status') == 'Waiting') {

				$name= $result->name.' '.$result->last_name;

				if($result->language == 'sp'){

					$subject = "Estado de su solicitud para el GProCongress II";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>Gracias por registrarse para participar del GProCongress II.</p><p><br></p><p>Dado que evaluamos muchas solicitudes con diversos niveles de participación en la capacitación de pastores, su solicitud para asistir al GProCongress II ha sido colocada en lista de espera.</p><p><br></p><p>Usted debería recibir una actualización de nuestra parte, Dios mediante, (pronto/fecha específica/en un par de meses).&nbsp;</p><p><br></p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo para conectarse con algún miembro de nuestro equipo.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->language == 'fr'){
				
					$subject = "Statut de votre demande GProCongrès II";
					$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d’avoir postulé pour assister au GProCongrès II.</p><p>Comme nous évaluons de nombreuses candidatures avec différents niveaux d’implication de la formation des pasteurs, votre candidature pour assister au GProCongrès II a été placée sur une liste d’attente.&nbsp;</p><p>Vous devriez recevoir une mise à jour de notre part, si le Seigneur le veut, (bientôt/par date précise/dans quelques mois).&nbsp;</p><p><br></p><p>Vous avez des questions ? Il suffit de répondre à cet e-mail pour communiquer avec un membre de l’équipe.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->language == 'pt'){
				
					$subject = "Estado da sua inscrição ao II CongressoGPro";
					$msg = '<p>Prezado, '.$name.',</p><p><br></p><p><br></p><p>Agradecemos pelo seu pedido para participar no II CongressoGPro.</p><p>A medida que avaliamos muitos pedidos com vários níveis de envolvimento no treinamento pastoral, o seu pedido de participação no II CongressoGPro foi colocado na lista de espera.</p><p>Você irá receber uma atualização da nossa parte, se o Senhor quiser, (brevemente/até data específica/ dentro de alguns meses).&nbsp;</p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail para se conectar com nosso membro da equipe.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Your GProCongress II application status';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for applying to attend the GProCongress II.</p><p>As we evaluate many applications with various levels of pastor training involvement, your application to attend the GProCongress II has been placed on a waiting list.&nbsp;</p><p>You should receive an update from us, the Lord willing, (soon/by specific date/ in a couple of months).</p><p><br></p><p>Have questions? Simply respond to this email to connect with a team member.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p><div><br></div>';
					
				}
				
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);
			}
			

			$result->save();

			$UserHistory=new \App\Models\UserHistory();
			$UserHistory->user_id=$result->id;
			$UserHistory->action_id=\Auth::user()->id;
			$UserHistory->action='User Profile '.$request->post('status');
			$UserHistory->save();

			if ($request->post('status') == 'Approved') {

				$name = $result->name.' '.$result->last_name;

				\App\Helpers\commonHelper::sendPaymentReminderMailSend($result->id,$result->email,$name);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

			}

			return response(array('error'=>false, 'reload'=>true, 'message'=>'Profile status change successful'), 200);
		
		} else {
			return response(array('error'=>true, 'reload'=>false, 'message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function commentToUser(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'user_id' => 'required|numeric|exists:users,id',
				'comment' => 'required',
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

				try {

					$data=new \App\Models\Comment();
					$data->sender_id = \Auth::user()->id;
					$data->receiver_id = $request->post('user_id');
					$data->comment = $request->post('comment');
					$data->save();

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->action_id=\Auth::user()->id;
					$UserHistory->user_id=$request->post('user_id');
					$UserHistory->action='Comment';
					$UserHistory->save();
	
					return response(array('reset'=>true, 'comment' => true, 'message'=>'Comment has been sent successfully.'), 200);

				} catch (\Throwable $th) {
					return response(array('message'=>'Something went wrong, please try again'), 500);
				}
			
			}
		} else if($request->ajax() && $request->isMethod('get')) {
			
			$columns = \Schema::getColumnListing('comments');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->orderBy('id', 'desc');

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('comment_by', function($data){

				return \App\Helpers\commonHelper::getUserNameById($data->sender_id);;
			})

			->addColumn('comment', function($data){
				return $data->comment;
			})

			->addColumn('created_at', function($data){
				return date('Y-m-d h:i', strtotime($data->created_at));
			})

			->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
			->make(true);
	
		}

	}

	public function userHistoryList(Request $request) {
	
		$columns = \Schema::getColumnListing('user_histories');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\UserHistory::where('user_id', $request->input('user_id'))->orderBy('id', 'desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\UserHistory::where('user_id', $request->input('user_id'))->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)
		->addColumn('action', function($data){
			return $data->action;
		})
		->addColumn('admin', function($data){

			if($data->action_id){

				
				return \App\Helpers\commonHelper::getUserNameById($data->action_id);
			}else{

				return \App\Helpers\commonHelper::getUserNameById($data->user_id);
			}
			
		})
		
		->addColumn('date', function($data){
			return date('d M Y', strtotime($data->created_at));
		})

		->addColumn('time', function($data){
			return date('H:i a', strtotime($data->created_at));
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);
	
	}
	
	public function getProfileBasePrice(Request $request) {

		if ($request->ajax()) {

			$basePrice = 0; $Spouse = []; $category = []; $trainer = '';

			$user = \App\Models\User::where('id', $request->post('id'))->first();

			if($user){

				$citizenship = $user->citizenship;

				if($request->post('citizenship')){

					$citizenship = $request->post('citizenship');

				}
				
				$countryPrice=\App\Models\Pricing::where('country_id',$citizenship)->first();

				$Spouse = \App\Models\User::where('parent_id', $request->post('id'))->where('added_as', 'Spouse')->first();
				
				if($user->marital_status == 'Unmarried'){

					$data = \App\Helpers\commonHelper::getBasePriceOfUnmarried($user->room,$countryPrice->base_price);
					
					$basePrice = $data ['basePrice'];
					$category = $data ['category'];


				}else if($user->marital_status == 'Married' && !$Spouse){

					$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWOSpouse($user->room,$countryPrice->base_price);

					$basePrice = $data ['basePrice'];
					$category = $data ['category'];

				}else if($user->marital_status == 'Married' && $Spouse){

					$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWSpouse($user->doyouseek_postoral,$Spouse->doyouseek_postoral,$user->ministry_pastor_trainer,$Spouse->ministry_pastor_trainer,$countryPrice->base_price);

					$basePrice = $data ['basePrice'];
					$category = $data ['category'];
					$trainer = $data ['trainer'];
				}

				$Offers = \App\Models\Offer::where([
					['status','=','1'],
					['start_date','<=',date('Y-m-d')],
					['end_date','>=',date('Y-m-d')]
					])->orderBy('id','desc')->get();

				$country = \App\Models\Pricing::orderBy('country_name', 'asc')->get();

				$html=view('admin.user.stage.stage_one_profile_status_model',compact('basePrice','Offers','category','trainer','country','user','citizenship'))->render();

				return response()->json(array('html'=>$html));
				

			}
			
        }

	}
	
	public function getOfferPrice(Request $request) {

		if ($request->ajax()) {

			$couponResult=\App\Models\Offer::where([
				['id','=',$request->id],
				['status','=','1'],
				['start_date','<=',date('Y-m-d')],
				['end_date','>=',date('Y-m-d')]
				])->first();

			if(!$couponResult){

				return response()->json(array('error'=>true,'message'=>'This Offer limit has been exceed.'));

			}else{

				$amount = \App\Helpers\commonHelper::getOfferDiscount($request->id,$request->amount);
			
				return response()->json(array('error'=>false,'amount'=>$amount));
			}
			
        }

	}

    public function spousePending(Request $request) {
		
		if ($request->ajax()) { 

			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			
			$query = \App\Models\User::where([['designation_id', 2], ['spouse_confirm_status', 'Pending'], ['spouse_confirm_token','!=','']])
						->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', 2], ['spouse_confirm_status', 'Pending'], ['spouse_confirm_token','!=','']]);


			if (request()->has('email')) {
				$totalData1->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name.' '.$data->last_name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('profile', function($data){
				if ($data->spouse_confirm_status == 'Pending') {
					
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
					
				} else if ($data->spouse_confirm_status == 'Approve') {

					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}
		    })

			// ->addColumn('status', function($data){
			// 	if($data->status=='1'){ 
			// 		$checked = "checked";
			// 	}else{
			// 		$checked = " ";
			// 	}

			// 	return '<div class="media-body icon-state switch-outline">
			// 				<label class="switch">
			// 					<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
			// 				</label>
			// 			</div>';
		    // })

			->addColumn('action', function($data){
				
				
					
				return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></div>';

				
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }


	}

	public function stageAllDownloadExcelFile(Request $request){

		// try{

			$result = \App\Models\User::with('TravelInfo')->where([['designation_id', 2], ['parent_id', NULL], ['added_as', NULL]])->orderBy('updated_at', 'desc')->get();
			
			if($result->count()==0){

				return response(array('error'=>true,'message'=>'Data not found.'),200);

			}else{

				
					$delimiter = ",";  
					$filename = "User-".date('d-m-Y').rand(111,999).".csv";
					
					$f = fopen('php://memory', 'w'); 
					
					//$f = fopen('php://memory', 'w');
					$fields = array('Id', 'Current Stage', 
										'Candidate Name', 
										'Candidate email Address', 
										'Candidate Mobile Number', 
										'Spouse Name', 
										'Spouse Email Address', 
										'Group Leader Name', 
										'Group Leader Email Address', 
										'Room Type', 
										'Pastor Trainer(yes/No)', 
										'Ministry Name', 
										'Total Payable Amount', 
										'Pending Amount', 
										'Accepted Amount', 
										'Payment in process', 
										'Payment Declined', 
										'Early Bird', 
										'Arrival date', 
										'Departure Date', 
										'Cab needed on Arrival', 
										'Cab needed on Departure', 
										'Stage 0', 
										'Stage 1', 
										'Stage 2', 
										'Stage 3', 
										'Stage 4', 
										'Stage 5'); 
					fputcsv($f, $fields, $delimiter); 

					$i=1;
					foreach($result as $row){
						
						if($row['stage'] == 0){
							$stage0 = "In Process";
						}elseif($row['stage'] > 0){
							$stage0 = "Completed";
						}else{
							$stage0 = "Pending";
						}  

						if($row['stage'] == 1){
							$stage1 = "In Process";
						}elseif($row['stage'] > 1){
							$stage1 = "Completed";
						}else{
							$stage1 = "Pending";
						} 

						if($row['stage'] == 2){
							$stage2 = "In Process";
						}elseif($row['stage'] > 2){
							$stage2 = "Completed";
						}else{
							$stage2 = "Pending";
						} 

						if($row['stage'] == 3){
							$stage3 = "In Process";
						}elseif($row['stage'] > 3){
							$stage3 = "Completed";
						}else{
							$stage3 = "Pending";
						} 

						if($row['stage'] == 4){
							$stage4 = "In Process";
						}elseif($row['stage'] > 4){
							$stage4 = "Completed";
						}else{
							$stage4 = "Pending";
						} 

						if($row['stage'] == 5){
							$stage5 = "In Process";
						}elseif($row['stage'] > 5){
							$stage5 = "Completed";
						}else{
							$stage5 = "Pending";
						} 

						$userSpouse = \App\Models\User::with('TravelInfo')->where([['parent_id', $row['id']], ['added_as', 'Spouse']])->first();
						$userGroup = \App\Models\User::where([['parent_id', $row['id']],['added_as', 'Group']])->first();
						$spouseName = '';
						$spouseEmail = '';
						$arrivalDate = '';
						$departureDate = '';
						$cabNeededOnArrival = '';
						$cabNeededOnDeparture = '';
						$mobile = '';
						$groupLeaderEmail= '';
						$groupLeaderName= '';
						if($userGroup){
							$groupLeaderName= $row['name'].' '.$row['last_name'];
							$groupLeaderEmail= $row['email'];
						}if($userSpouse){
							$spouseName= $userSpouse->name.' '.$userSpouse->last_name;
							$spouseEmail= $userSpouse->email;
						}

						
						if($row['TravelInfo'] && $row['TravelInfo']['flight_details']) {
							$flight_details = json_decode($row['TravelInfo']['flight_details']);
							$cabNeededOnArrival = $row['TravelInfo']['logistics_dropped'];
							$cabNeededOnDeparture = $row['TravelInfo']['logistics_picked'];
							if(!empty($flight_details)){
								$arrivalDate = date('d-m-Y',strtotime($flight_details->arrival_date_arrival));
								$departureDate = date('d-m-Y',strtotime($flight_details->departure_date_departure));
							}
							
						}

						if($row['mobile']){
							$mobile = '+'.$row['phone_code'].' '.$row['mobile'];
						}

						$lineData = array(($i), 'Stage '.$row['stage'], 
						$row['name'].' '.$row['last_name'], 
						$row['email'], 
						$mobile, 
						$spouseName,
						$spouseEmail,
						$groupLeaderName,
						$groupLeaderEmail,
						$row['room'] ?? 'Double Deluxe',
						$row['ministry_pastor_trainer'],
						$row['ministry_name'],
						$row['amount'],
						\App\Helpers\commonHelper::getTotalPendingAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAcceptedAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAmountInProcess($row['id']),
						\App\Helpers\commonHelper::getTotalRejectedAmount($row['id']),
						$row['early_bird'],
						$arrivalDate,
						$departureDate,
						$cabNeededOnArrival,
						$cabNeededOnDeparture,
						$stage0, $stage1, 
						$stage2, $stage3, $stage4, $stage5); 
						
						fputcsv($f, $lineData, $delimiter); 
						
						$results = \App\Models\User::with('TravelInfo')->where([['parent_id', $row['id']]])->get();
						$groupLeaderName = '';
						$groupLeaderEmail = '';
						$spouseLeaderName = '';
						$spouseLeaderEmail = '';
						$arrivalDate = '';
						$departureDate = '';
						$cabNeededOnDeparture = '';
						$cabNeededOnArrival = '';
						
						if(!empty($results) && count($results)>0){
								$j = 1;
							foreach($results as $val){

								if(isset($val['TravelInfo']) && $val['TravelInfo']['flight_details']) {
									$flight_details = json_decode($val['TravelInfo']['flight_details']);
									$cabNeededOnArrival = $val['TravelInfo']['logistics_dropped'];
									$cabNeededOnDeparture = $val['TravelInfo']['logistics_picked'];
									if(!empty($flight_details)){
										$arrivalDate = date('d-m-Y',strtotime($flight_details->arrival_date_arrival));
										$departureDate = date('d-m-Y',strtotime($flight_details->departure_date_departure));
									}
								}


								if($val && $val->added_as == 'Group'){

									$groupLeaderName= $row['name'].' '.$row['last_name'];
									$groupLeaderEmail= $row['email'];
								}
								if($val && $val->added_as == 'Spouse'){
									
									$spouseLeaderName= $row['name'].' '.$row['last_name'];
									$spouseLeaderEmail= $row['email'];
									$groupLeaderName = '';
									$groupLeaderEmail = '';
								}


								if($val['stage'] == 0){
									$stage0 = "In Process";
								}elseif($val['stage'] > 0){
									$stage0 = "Completed";
								}else{
									$stage0 = "Pending";
								}  

								if($val['stage'] == 1){
									$stage1 = "In Process";
								}elseif($val['stage'] > 1){
									$stage1 = "Completed";
								}else{
									$stage1 = "Pending";
								} 

								if($val['stage'] == 2){
									$stage2 = "In Process";
								}elseif($val['stage'] > 2){
									$stage2 = "Completed";
								}else{
									$stage2 = "Pending";
								} 

								if($val['stage'] == 3){
									$stage3 = "In Process";
								}elseif($val['stage'] > 3){
									$stage3 = "Completed";
								}else{
									$stage3 = "Pending";
								} 

								if($val['stage'] == 4){
									$stage4 = "In Process";
								}elseif($val['stage'] > 4){
									$stage4 = "Completed";
								}else{
									$stage4 = "Pending";
								} 

								if($val['stage'] == 5){
									$stage5 = "In Process";
								}elseif($val['stage'] > 5){
									$stage5 = "Completed";
								}else{
									$stage5 = "Pending";
								} 


								if($val['mobile']){
									$mobile = '+'.$val['phone_code'].' '.$val['mobile'];
								}

								$lineData = array(($i), 'Stage '.$val['stage'], 
									$val['name'].' '.$val['last_name'], 
									$val['email'], 
									$mobile, 
									$spouseLeaderName,
									$spouseLeaderEmail,
									$groupLeaderName,
									$groupLeaderEmail,
									$val['room'] ?? 'Double Deluxe',
									$val['ministry_pastor_trainer'],
									$val['ministry_name'],
									$val['amount'],
									\App\Helpers\commonHelper::getTotalPendingAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAcceptedAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAmountInProcess($val['id']),
									\App\Helpers\commonHelper::getTotalRejectedAmount($val['id']),
									$val['early_bird'],
									$arrivalDate,
									$departureDate,
									$cabNeededOnArrival,
									$cabNeededOnDeparture,
									$stage0, $stage1, 
									$stage2, $stage3, $stage4, $stage5); 
								fputcsv($f, $lineData, $delimiter);

								$j++;
							}
						}

						$i++;
					}
					
					fseek($f, 0); 
					header('Content-Type: text/csv'); 
					// header("Content-Type: application/octet-stream");
					header('Content-Disposition: attachment; filename="' . $filename . '";');
					fpassthru($f);
					

					fclose($f);
					
					//readfile ($filename);

				
				exit; 

				return response(array('error'=>false,"message" => "File downloaded success"),200); 
				
			}
			
		// }catch (\Exception $e){
		
		// 	return response(array('error'=>true,"message" => "Something went wrong.please try again"),200); 
		
		// }

	}

    public function refundAmount(Request $request){
 
        if($request->ajax()){

			$rules = [
				'reference_number' => 'required',
				'amount' => 'required|numeric',
				'user_id' => 'required|numeric',
			];
	
			$validator = \Validator::make($request->all(), $rules);
				
			if ($validator->fails()) {
				$message = [];
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message = $msg[0];
					break;
				}
				
				return response(array("error"=>true, 'message'=>$message), 403);
				
			}else{
	
				try {

					$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
					if($referenceNumberCheck){

						return response(array("error"=>true, "message"=>'Transaction already exists'), 403);

					}else{

						$totalPendingAmount = \App\Helpers\commonHelper::userBalance($request->post('user_id'));
						
						if($request->post('amount') <= $totalPendingAmount){
		
							$transactionId=strtotime("now").rand(11,99);
		
							$orderId=strtotime("now").rand(11,99);
			
							$transaction = new \App\Models\Transaction();
							$transaction->user_id = $request->post('user_id');
							$transaction->bank = 'Wire';
							$transaction->order_id = $orderId;
							$transaction->transaction_id = $transactionId;
							$transaction->method = 'Manual';
							$transaction->amount = $request->post('amount');
							$transaction->bank_transaction_id = $request->post('reference_number');
							$transaction->payment_status = '3';
							$transaction->status = '3';
							$transaction->particular_id = '4';
							$transaction->save();
		
							$Wallet = new \App\Models\Wallet();
							$Wallet->user_id = $request->post('user_id');
							$Wallet->type  = 'Dr';
							$Wallet->amount = $request->post('amount');
							$Wallet->transaction_id = $transaction->id;
							$Wallet->status = 'Success';
							$Wallet->save();
			
							$user = \App\Models\User::where('id',$request->post('user_id'))->first();
							$user->status = '1';
							$user->save();

							$to = $user->email;
							$name = $user->name.' '.$user->last_name;
							$amount = $request->post('amount');
							$subject = 'Payment refund';
							$msg = 'Your '.$request->post('amount').' Payment refund successfully';
							\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		
							$subject = '[GProCongress II Admin]  Payment refund';
							$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
							\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);
		
							// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
		
							return response(array("error"=>false, "message"=>'Manual payment refund successful'), 200);
			
						}else{
		
							return response(array("error"=>true, "message"=>'Payment not refund'), 403);
			
						}
						
					}
	
					
					
				} catch (\Exception $e) {
					return response(array("error"=>true, "message"=>$e->getMessage()), 403);
				}
			}

            
        }
        
    }

    public function sponsoredRefundAmount(Request $request){
 
        if($request->ajax()){

			$rules = [
				
				'amount' => 'required|numeric',
				'user_id' => 'required|numeric',
				'other_user' => 'required|in:Yes,No',
			];

			if($request->post('other_user') == 'Yes'){

				$rules['other_user_id'] = 'required|numeric';

			}else{

				$rules['reference_number'] = 'required';
			}
	
			$validator = \Validator::make($request->all(), $rules);
				
			if ($validator->fails()) {
				$message = [];
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message = $msg[0];
					break;
				}
				
				return response(array("error"=>true, 'message'=>$message), 403);
				
			}else{
	
				try {

					if($request->post('other_user') == 'Yes'){

						$transactionId=strtotime("now").rand(11,99);
	
						$orderId=strtotime("now").rand(11,99);
		
						$transaction = new \App\Models\Transaction();
						$transaction->user_id = $request->post('user_id');
						$transaction->bank = 'Wire';
						$transaction->order_id = $orderId;
						$transaction->transaction_id = $transactionId;
						$transaction->method = 'Manual';
						$transaction->amount = $request->post('amount');
						$transaction->payment_status = '3';
						$transaction->status = '3';
						$transaction->particular_id = '4';
						$transaction->save();
	
						$Wallet = new \App\Models\Wallet();
						$Wallet->user_id = $request->post('user_id');
						$Wallet->type  = 'Dr';
						$Wallet->amount = $request->post('amount');
						$Wallet->transaction_id = $transaction->id;
						$Wallet->status = 'Success';
						$Wallet->save();
		
						$user = \App\Models\User::where('id',$request->post('user_id'))->first();
						$user->status = '1';
						$user->save();

						$to = $user->email;
						$name = $user->name.' '.$user->last_name;
						$amount = $request->post('amount');
						$subject = 'Payment refund';
						$msg = 'Your '.$request->post('amount').' Payment refund  successfully';
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
	
						$subject = '[GProCongress II Admin]  Payment refund';
						$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
						\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);


						$transactionId=strtotime("now").rand(11,99);
	
						$orderId=strtotime("now").rand(11,99);
						
						$transaction = new \App\Models\Transaction();
						$transaction->user_id = $request->post('other_user_id');
						$transaction->bank = 'Wire';
						$transaction->order_id = $orderId;
						$transaction->transaction_id = $transactionId;
						$transaction->method = 'Manual';
						$transaction->amount = $request->post('amount');
						$transaction->payment_status = '2';
						$transaction->status = '1';
						$transaction->particular_id = '2';
						$transaction->save();
	
						$Wallet = new \App\Models\Wallet();
						$Wallet->user_id = $request->post('other_user_id');
						$Wallet->type  = 'Cr';
						$Wallet->amount = $request->post('amount');
						$Wallet->transaction_id = $transaction->id;
						$Wallet->status = 'Success';
						$Wallet->save();

					}else{

						$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
						if($referenceNumberCheck){

							return response(array("error"=>true, "message"=>'Transaction already exists'), 403);

						}else{

							if($request->post('amount') > 0){
			
								$transactionId=strtotime("now").rand(11,99);
			
								$orderId=strtotime("now").rand(11,99);
				
								$transaction = new \App\Models\Transaction();
								$transaction->user_id = $request->post('user_id');
								$transaction->bank = 'Wire';
								$transaction->order_id = $orderId;
								$transaction->transaction_id = $transactionId;
								$transaction->method = 'Manual';
								$transaction->amount = $request->post('amount');
								$transaction->bank_transaction_id = $request->post('reference_number');
								$transaction->payment_status = '3';
								$transaction->status = '3';
								$transaction->particular_id = '4';
								$transaction->save();
			
								$Wallet = new \App\Models\Wallet();
								$Wallet->user_id = $request->post('user_id');
								$Wallet->type  = 'Dr';
								$Wallet->amount = $request->post('amount');
								$Wallet->transaction_id = $transaction->id;
								$Wallet->status = 'Success';
								$Wallet->save();
				
								$user = \App\Models\User::where('id',$request->post('user_id'))->first();
								$user->status = '1';
								$user->save();

								$to = $user->email;
								$subject = 'Payment refund';
								$msg = 'Your '.$request->post('amount').' Payment refund  successfully';
								\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
			
								$to = $user->email;
								$name = $user->name.' '.$user->last_name;
								$amount = $request->post('amount');
								
								$subject = '[GProCongress II Admin]  Payment refund';
								$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
								\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

			
								// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
			
								return response(array("error"=>false, "message"=>'Manual payment refund successful'), 200);
				
							}else{
			
								return response(array("error"=>true, "message"=>'Amount should be grater then to zeo'), 403);
				
							}
							
						}
	
					}
					
				} catch (\Exception $e) {

					return response(array("error"=>true, "message"=>$e->getMessage()), 403);
				}
			}

            
        }
        
    }

	public function uploadDraftInformation(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){

			$rules = [
				'id' => 'numeric|required',
				'file' => 'required',
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

				$TravelInfo = \App\Models\TravelInfo::where('id',$request->post('id'))->first();

				if (!$TravelInfo) {

					return response(array("error"=>true, 'message'=>'Data not found'), 403);

				} else {

					$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.id',$request->post('id'))->first();
		
		
					if($request->hasFile('file')){
						$imageData = $request->file('file');
						$image = 'draft_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
						$destinationPath = public_path('/uploads/file');
						$imageData->move($destinationPath, $image);

						$TravelInfo->draft_file = $image;
					} 
					
					$TravelInfo->save();
					
					$to = $result->email;
					
					if($result->language == 'sp'){

						$subject = "Por favor, verifique su información de viaje";
						$msg = '<p>Estimado '.$result->name.' '.$result->last_name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su información de viaje.&nbsp;</p><p><br></p><p>A continuación, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la información recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: &lt;enlace&gt; para verificar que la información es correcta.</p><p><br></p><p>Gracias por su colaboración.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
					
					}elseif($result->language == 'fr'){
					
						$subject = "Veuillez vérifier vos informations de voyage";
						$msg = "<p>Cher '.$result->name.' '.$result->last_name.',&nbsp;</p><p><br></p><p>Merci d’avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons rédigée basée sur les informations reçues.&nbsp;</p><p><br></p><p>Pourriez-vous s’il vous plaît examiner la lettre, puis cliquer sur ce lien: &lt;lien&gt; pour vérifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe du GProCongrès II</p>";
			
					}elseif($result->language == 'pt'){
					
						$subject = "Por favor verifique sua Informação de Viagem";
						$msg = '<p>Prezado '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Agradecemos por submeter sua informação de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informação que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e daí clicar neste link: &lt;link&gt; para verificar que a informação esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Please submit your travel information.';
						$msg = '<p>Dear '.$result->name.' '.$result->last_name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
											
					}

					$attachment = public_path('/uploads/file/'.$TravelInfo->draft_file);

					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, false, $attachment);
					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
					
					return response(array('message'=>'Draft file has been sent successfully.'), 200);
				}

			}
			
		}

	}

	public function uploadFinalInformation(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){

			$rules = [
				'id' => 'numeric|required',
				'type' => 'numeric|required|in:1,2',
			];

			if($request->post('type') == '2'){

				$rules['file'] = 'required';
			}

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

				$TravelInfo = \App\Models\TravelInfo::where('id',$request->post('id'))->first();
				$attachment = '';
				$pdf = '';

				if (!$TravelInfo) {

					return response(array("error"=>true, 'message'=>'Data not found'), 403);

				} else {

					$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.id',$request->post('id'))->first();
		
					$user = \App\Models\User::with('TravelInfo')->find($result->user_id);

					if($request->post('type') == '2'){

						if($request->hasFile('file')){
							$imageData = $request->file('file');
							$image = 'draft_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
							$destinationPath = public_path('/uploads/file');
							$imageData->move($destinationPath, $image);

							$TravelInfo->final_file = $image;
						} 

						
						$attachment = public_path('/uploads/file/'.$TravelInfo->final_file);


					}else{

						$user = \App\Models\User::with('TravelInfo')->find($result->user_id);
						
						$pdfData = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$result->user_id)->first();

						
						$pdf = \PDF::loadView('email_templates.travel_info', $pdfData->toArray());

					}

					$user->stage = 4;
					$user->save();
					$to = $user->email;
					
					$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
					
					if($resultSpouse){

						$resultSpouse->stage = 4;
						$resultSpouse->save();
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($resultSpouse->id,'Travel Info Approved','Travel Info Approved','Travel Info Approved',\Auth::user()->id);
						
					}

					$subject = 'Travel Info Approved';
					$msg = 'Your travel info has been approved successfully';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf, $attachment);
					// \App\Helpers\commonHelper::sendSMS($user->mobile);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Travel Info Approved',\Auth::user()->id);
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					
					$subject = 'Session information ';
					$msg = 'Your Travel Information has been approved successfully, Please session information can be updated now';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Session information',\Auth::user()->id);
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					
					$TravelInfo->admin_status = '1';
					
					$TravelInfo->save();
					
					return response(array('message'=>'Visa letter approved successfully'), 200);
				}

			}
			
		}

	}

	public function cashPaymentSubmit(Request $request){
 
        if($request->ajax()){

            $rules = [
                'amount' => 'required|numeric',
                'user_id' => 'required|numeric',
                'remark' => 'string|required',
            ];
    
            $validator = \Validator::make($request->all(), $rules);
                
            if ($validator->fails()) {
                $message = [];
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message = $msg[0];
                    break;
                }
                
                return response(array("error"=>true, 'message'=>$message), 403);
                
            }else{
    
                try {

                    $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->post('user_id'), false); 
                    if($request->post('amount') <= $totalPendingAmount){

                        $transactionId=strtotime("now").rand(11,99);

                        $orderId=strtotime("now").rand(11,99);
        
                        $transaction = new \App\Models\Transaction();
                        $transaction->user_id = $request->post('user_id');
                        $transaction->bank = 'Cash';
                        $transaction->order_id = $orderId;
                        $transaction->transaction_id = $transactionId;
                        $transaction->method = 'Offline';
                        $transaction->amount = $request->post('amount');
                        $transaction->description = $request->post('remark');
                        $transaction->status = '0';
                        $transaction->particular_id = '1';
                        $transaction->save();

                        $Wallet = new \App\Models\Wallet();
                        $Wallet->user_id = $request->post('user_id');
                        $Wallet->type  = 'Cr';
                        $Wallet->amount = $request->post('amount');
                        $Wallet->transaction_id = $transaction->id;
                        $Wallet->status = 'Pending';
                        $Wallet->save();
        
                        $user = \App\Models\User::where('id',$request->post('user_id'))->first();

                        $to = $user->email;
                        $name = $user->name.' '.$user->last_name;
                        $amount = $request->post('amount');
                        
                        $subject = 'Transaction Complete';
                        $msg = 'Your '.$amount.' transaction has been send successfully';
                        \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

                        $subject = '[GProCongress II Admin] || Payment Received';
                        $msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has made Payment of&nbsp; '.$amount.' for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Payment Mode: Cash</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
                        \App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

                        // \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

                        
                        return response(array("error"=>false, "message"=>'Cash payment added successful'), 200);
        
                    }else{

                        return response(array("error"=>true, "message"=>'payment already paid'), 403);
        
                    }
                      
                } catch (\Exception $e) {
                    return response(array("error"=>true, "message"=>$e->getMessage()), 403);
                }
            }

        }
        
    }

	public function archiveUser(Request $request,$id){
	
		try{

			$exitsUser = \App\Models\User::find($id);

			$userSpouse = \App\Models\User::where('parent_id',$id)->get();

			if(!empty($userSpouse) && count($userSpouse)>0){

				$request->session()->flash('5fernsadminerror','User not delete! this user comming with spouse/ group');
				return redirect()->back();

			}else{

				$user = new \App\Models\ArchiveUser();

				$user->user_id=$exitsUser->id;
				$user->parent_id=$exitsUser->parent_id;
				$user->added_as=$exitsUser->added_as;
				$user->salutation=$exitsUser->salutation;
				$user->name=$exitsUser->name;
				$user->last_name=$exitsUser->last_name;
				$user->email=$exitsUser->email;
				$user->phone_code=$exitsUser->phone_code;
				$user->mobile=$exitsUser->mobile;
				$user->reg_type=$exitsUser->reg_type;
				$user->status=$exitsUser->status;
				$user->status_change_at=$exitsUser->status_change_at;
				$user->profile_status=$exitsUser->profile_status;
				$user->remark=$exitsUser->remark;
				$user->user_type=$exitsUser->user_type;
				$user->otp_verified=$exitsUser->otp_verified;
				$user->otp=$exitsUser->otp;
				$user->password=$exitsUser->password;
				$user->gender=$exitsUser->gender;
				$user->dob=$exitsUser->dob;
				$user->citizenship=$exitsUser->citizenship;
				$user->marital_status=$exitsUser->marital_status;
				$user->contact_address=$exitsUser->contact_address;
				$user->contact_zip_code=$exitsUser->contact_zip_code;
				$user->contact_country_id=$exitsUser->contact_country_id;
				$user->contact_state_id=$exitsUser->contact_state_id;
				$user->contact_city_id=$exitsUser->contact_city_id;
				$user->contact_business_codenumber=$exitsUser->contact_business_codenumber;
				$user->contact_business_number=$exitsUser->contact_business_number;
				$user->contact_whatsapp_codenumber=$exitsUser->contact_whatsapp_codenumber;
				$user->contact_whatsapp_number=$exitsUser->contact_whatsapp_number;
				$user->ministry_name=$exitsUser->ministry_name;
				$user->ministry_address=$exitsUser->ministry_address;
				$user->ministry_zip_code=$exitsUser->ministry_zip_code;
				$user->ministry_country_id=$exitsUser->ministry_country_id;
				$user->ministry_state_id=$exitsUser->ministry_state_id;
				$user->ministry_city_id=$exitsUser->ministry_city_id;
				$user->ministry_pastor_trainer=$exitsUser->ministry_pastor_trainer;
				$user->ministry_pastor_trainer_detail=$exitsUser->ministry_pastor_trainer_detail;
				$user->doyouseek_postoral=$exitsUser->doyouseek_postoral;
				$user->doyouseek_postoralcomment=$user->doyouseek_postoralcomment;
				$user->stage=$exitsUser->stage;
				$user->designation_id=$exitsUser->designation_id;
				$user->profile_update=$exitsUser->profile_update;
				$user->profile_updated_at=$exitsUser->profile_updated_at;
				$user->terms_and_condition=$exitsUser->terms_and_condition;
				$user->amount=$exitsUser->amount;
				$user->payment_status=$exitsUser->payment_status;
				$user->social_id=$exitsUser->social_id;
				$user->room=$exitsUser->room;
				$user->system_generated_password=$exitsUser->system_generated_password;
				$user->change_room_type=$exitsUser->change_room_type;
				$user->upgrade_category=$exitsUser->upgrade_category;
				$user->early_bird=$exitsUser->early_bird;
				$user->offer_id=$exitsUser->offer_id;
				$user->remember_token=$exitsUser->remember_token;
				$user->created_at=$exitsUser->created_at;
				$user->updated_at=$exitsUser->updated_at;
				$user->deleted_at=$exitsUser->deleted_at;
				$user->qrcode=$exitsUser->qrcode;
				$user->ministry_city_name=$exitsUser->ministry_city_name;
				$user->ministry_state_name=$exitsUser->ministry_state_name;
				$user->contact_city_name=$exitsUser->contact_city_name;
				$user->profile_submit_type=$exitsUser->profile_submit_type;
				$user->spouse_confirm_token=$exitsUser->spouse_confirm_token;
				$user->spouse_confirm_status=$exitsUser->spouse_confirm_status;
				$user->willing_to_commit=$exitsUser->willing_to_commit;
				$user->comment=$exitsUser->comment;
				$user->envision_training=$exitsUser->envision_training;
				$user->spouse_confirm_reminder_email=$exitsUser->spouse_confirm_reminder_email;
				$user->share_your_room_with=$exitsUser->share_your_room_with;
				$user->language=$exitsUser->language;
				$user->cash_payment_option=$exitsUser->cash_payment_option;
				$user->spouse_id=$exitsUser->spouse_id;

				$user->save();
				
				if ($exitsUser) {

					\App\Models\User::where('id',$id)->forceDelete();
					
					$request->session()->flash('5fernsadminsuccess','User deleted successfully.');

				}else{

					$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
				}
					
				return redirect()->back();
				
			}
			
				
			
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
		}
	}

    
	public function userMailTriggerList(Request $request) {
	
		$columns = \Schema::getColumnListing('user_mail_triggers');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\UserMailTrigger::where('user_id', $request->input('user_id'))->orderBy('id', 'desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\UserMailTrigger::where('user_id', $request->input('user_id'))->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)
		
		->addColumn('subject', function($data){
			return $data->subject;
		})

		->addColumn('date', function($data){
			return date('d M Y', strtotime($data->created_at));
		})

		->addColumn('time', function($data){
			return date('H:i a', strtotime($data->created_at));
		})

		->addColumn('action', function($data){
				
			if (\Auth::user()->designation_id == '1') {
				return '<div >
							<button type="button" style="width:41px" title="View message" class="btn btn-sm btn-primary px-3 m-1 text-white messageGet" data-id="'.$data->id.'" ><i class="fas fa-eye"></i></button>
						</div>';			
			}
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);
	
	}
	
	public function userMailTriggerListModel(Request $request) {
	
		if($request->ajax()){

			$UserMailTrigger = \App\Models\UserMailTrigger::where('id', $request->id)->first();

			return response(array('message'=>$UserMailTrigger->message), 200);

		}

		
	
	}

	public function userRecover(Request $request){
	
		try{

			if($request->ajax()){

				if($request->post('type') == '2'){

					$usertable = \App\Models\User::where('email','=',$request->post('email'))->first();
					if($usertable){

						if($usertable->stage != '2'){

							return response(array('message'=>'User stage moved not allowed'),403);
						}

						$userSpouse = \App\Models\User::where('parent_id',$usertable->id)->where('added_as','Spouse')->first();

						if($userSpouse){

							$userSpouse->profile_status='Review';
							$userSpouse->stage= '1';
							$userSpouse->amount= '0';
							$userSpouse->save();

						}

						
						$userHus = \App\Models\User::where('id',$usertable->parent_id)->first();

						if($userHus){

							$userHus->profile_status='Review';
							$userHus->stage= '1';
							$userHus->amount= '0';
							$userHus->save();

						}

						$usertable->profile_status='Review';
						$usertable->stage= '1';
						$usertable->amount= '0';
						$usertable->save();

						return response(array('message'=>'User stage move successfully','reset'=>true),200);

					}else{

						return response(array('message'=>'User does not exist'),403);
					}

				}elseif($request->post('type') == '1'){

					$usertable = \App\Models\User::where('email','=',$request->post('email'))->first();

					if($usertable){

						return response(array('message'=>'User already exist'),403);


					}else{

						$exitsUser = \App\Models\ArchiveUser::where('email',$request->post('email'))->first();

						if($exitsUser){

							$user = new \App\Models\User();

							$user->parent_id=$exitsUser->parent_id;
							$user->added_as=$exitsUser->added_as;
							$user->salutation=$exitsUser->salutation;
							$user->name=$exitsUser->name;
							$user->last_name=$exitsUser->last_name;
							$user->email=$exitsUser->email;
							$user->phone_code=$exitsUser->phone_code;
							$user->mobile=$exitsUser->mobile;
							$user->reg_type=$exitsUser->reg_type;
							$user->status=$exitsUser->status;
							$user->status_change_at=$exitsUser->status_change_at;
							$user->profile_status=$exitsUser->profile_status;
							$user->remark=$exitsUser->remark;
							$user->user_type=$exitsUser->user_type;
							$user->otp_verified=$exitsUser->otp_verified;
							$user->otp=$exitsUser->otp;
							$user->password=$exitsUser->password;
							$user->gender=$exitsUser->gender;
							$user->dob=$exitsUser->dob;
							$user->citizenship=$exitsUser->citizenship;
							$user->marital_status=$exitsUser->marital_status;
							$user->contact_address=$exitsUser->contact_address;
							$user->contact_zip_code=$exitsUser->contact_zip_code;
							$user->contact_country_id=$exitsUser->contact_country_id;
							$user->contact_state_id=$exitsUser->contact_state_id;
							$user->contact_city_id=$exitsUser->contact_city_id;
							$user->contact_business_codenumber=$exitsUser->contact_business_codenumber;
							$user->contact_business_number=$exitsUser->contact_business_number;
							$user->contact_whatsapp_codenumber=$exitsUser->contact_whatsapp_codenumber;
							$user->contact_whatsapp_number=$exitsUser->contact_whatsapp_number;
							$user->ministry_name=$exitsUser->ministry_name;
							$user->ministry_address=$exitsUser->ministry_address;
							$user->ministry_zip_code=$exitsUser->ministry_zip_code;
							$user->ministry_country_id=$exitsUser->ministry_country_id;
							$user->ministry_state_id=$exitsUser->ministry_state_id;
							$user->ministry_city_id=$exitsUser->ministry_city_id;
							$user->ministry_pastor_trainer=$exitsUser->ministry_pastor_trainer;
							$user->ministry_pastor_trainer_detail=$exitsUser->ministry_pastor_trainer_detail;
							$user->doyouseek_postoral=$exitsUser->doyouseek_postoral;
							$user->doyouseek_postoralcomment=$user->doyouseek_postoralcomment;
							$user->stage=$exitsUser->stage;
							$user->designation_id=$exitsUser->designation_id;
							$user->profile_update=$exitsUser->profile_update;
							$user->profile_updated_at=$exitsUser->profile_updated_at;
							$user->terms_and_condition=$exitsUser->terms_and_condition;
							$user->amount=$exitsUser->amount;
							$user->payment_status=$exitsUser->payment_status;
							$user->social_id=$exitsUser->social_id;
							$user->room=$exitsUser->room;
							$user->system_generated_password=$exitsUser->system_generated_password;
							$user->change_room_type=$exitsUser->change_room_type;
							$user->upgrade_category=$exitsUser->upgrade_category;
							$user->early_bird=$exitsUser->early_bird;
							$user->offer_id=$exitsUser->offer_id;
							$user->remember_token=$exitsUser->remember_token;
							$user->created_at=$exitsUser->created_at;
							$user->updated_at=$exitsUser->updated_at;
							$user->deleted_at=$exitsUser->deleted_at;
							$user->qrcode=$exitsUser->qrcode;
							$user->ministry_city_name=$exitsUser->ministry_city_name;
							$user->ministry_state_name=$exitsUser->ministry_state_name;
							$user->contact_city_name=$exitsUser->contact_city_name;
							$user->profile_submit_type=$exitsUser->profile_submit_type;
							$user->spouse_confirm_token=$exitsUser->spouse_confirm_token;
							$user->spouse_confirm_status=$exitsUser->spouse_confirm_status;
							$user->willing_to_commit=$exitsUser->willing_to_commit;
							$user->comment=$exitsUser->comment;
							$user->envision_training=$exitsUser->envision_training;
							$user->spouse_confirm_reminder_email=$exitsUser->spouse_confirm_reminder_email;
							$user->share_your_room_with=$exitsUser->share_your_room_with;
							$user->language=$exitsUser->language;
							$user->cash_payment_option=$exitsUser->cash_payment_option;
							$user->spouse_id=$exitsUser->spouse_id;

							$user->save();
						
							if ($exitsUser) {

								\App\Models\ArchiveUser::where('id',$exitsUser->id)->forceDelete();
								
								return response(array('message'=>'User Recover successfully.','reset'=>true),200);


							}else{

								return response(array('message'=>'Something went wrong. Please try again'),403);
							}

						}else{

							return response(array('message'=>'User does not exist'),403);

						}
						
					}
				}

			}
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
		}

		if(\Auth::user()->email ==  'german@gprocongress.org' || \Auth::user()->email == 'admin@gmail.com'){

			$result=[];
        	return view('admin.user.user_recover',compact('result'));
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);

		}
		

	}

	public function TransationDataExport(Request $request){
	
		try{
			
			$delimiter = ","; 
			$filename = "transactiondata-data_" . date('Y-m-d') . ".csv"; 
			
			$f = fopen('php://memory', 'w'); 
			
			$fields = array('ID', 'Name', 'Email Address', 'Mobile Number', 'Total Amount', 'Amount In Process', 'Accepted Amount', 'Declined Amount', 'Pending Amount', 'Payment Status'); 
			fputcsv($f, $fields, $delimiter); 
			
			$query = \App\Models\Transaction::orderBy('id','desc');

			$data = $query->get();
			foreach($data as $key=>$data){
				

				$lineData = array($key+1, \App\Helpers\commonHelper::getUserNameById($data->user_id), $data->bank, $data->country_of_sender, $data->method, $data->order_id, $data->bank_transaction_id, $data->amount,\App\Helpers\commonHelper::getPaymentStatusName($data->payment_status)); 
				fputcsv($f, $lineData, $delimiter); 
			}
			
			fseek($f, 0); 
			
			header('Content-Type: text/csv'); 
			header('Content-Disposition: attachment; filename="' . $filename . '";'); 
			
			fpassthru($f); 

									
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
		}


	}
}
