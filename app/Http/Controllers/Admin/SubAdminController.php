<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SubAdminController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name' => 'string|required',
				'password' => 'required',
				'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->where(function ($query) use($request) {
					return $query->where('id', '!=', $request->id)->where('deleted_at', NULL);
				})],
				'designation_id'=>'required|exists:designations,id'
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

                if ((int) $request->post('id') > 0) {
                    $data=\App\Models\User::find($request->post('id'));
                } else {
                    $data=new \App\Models\User();
                }	

				$data->email = $request->post('email');
				$data->name = $request->post('name');
				$data->reg_type = 'admin';
				$data->user_type = '1';
				$data->designation_id = $request->post('designation_id');
				$data->password = \Hash::make($request->post('password'));
				$data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Sub Admin added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Sub Admin updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$designations = \App\Models\Designation::where('slug','recruiter')->orWhere('slug','registrar')->orWhere('slug','admin')->orWhere('slug','finances')->orWhere('slug','exhibitor')->orWhere('slug','visa')->where('status','1')->get();
		
		$result = array();
        return view('admin.subadmin.add', compact('result','designations'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\User::orderBy($order,$dir)->where('user_type','1')->where('id','!=','1');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where('user_type','1')->where('id','!=','1');

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

			->addColumn('email', function($data){
				return $data->email;
		    })
			->addColumn('name', function($data){
				return $data->name;
		    })
			->addColumn('designations', function($data){

				$designations = \App\Models\Designation::where('id',$data->designation_id)->first();
		
				return $designations->designations;
		    })

			->addColumn('status', function($data){
				if ($data->status=='1') { 
					$checked = "checked";
				} else {
					$checked = " ";
				}

				return '<div class="media-body icon-state switch-outline">
							<label class="switch">
								<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
							</label>
						</div>';
		    })

			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this Sub Admin ?'";
				
				return '<a href="'.route('admin.subadmin.edit', ['id' => $data->id] ).'" title="Edit Sub Admin" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.subadmin.delete', ['id' => $data->id] ).'" title="Delete Sub Admin" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.subadmin.list');

	}

    public function edit($id) {
		
		$result = \App\Models\User::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		$designations = \App\Models\Designation::where('slug','recruiter')->orWhere('slug','registrar')->orWhere('slug','admin')->orWhere('slug','finances')->orWhere('slug','exhibitor')->orWhere('slug','visa')->where('status','1')->get();
		
		return view('admin.subadmin.add')->with(compact('result','designations'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\User::find($id);
		
		if ($result) {

			\App\Models\User::where('id', $id)->delete();
			$request->session()->flash('success','Sub Admin deleted successfully.');

		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Sub Admin status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
