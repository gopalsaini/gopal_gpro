<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class RoleController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name' => 'string|required',
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
                    $data=\App\Models\Role::find($request->post('id'));
                } else {
                    $data=new \App\Models\Role();
                }	

				$data->name = $request->post('name');
				$data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Role added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Role updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.role.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Role::orderBy($order,$dir)->where('id','!=','1');

			if (request()->has('email')) {
				$query->where('name', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\Role::where('id','!=','1');

			if (request()->has('email')) {
				
				$totalData1->where('name', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name;
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
				$msg = "' Are you sure to delete this Role ?'";
				
				return '<a href="'.route('admin.role.edit', ['id' => $data->id] ).'" title="Edit Role" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.role.delete', ['id' => $data->id] ).'" title="Delete Role" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.role.list');

	}

    public function edit(Request $request,$id) {
		
		$result = \App\Models\Role::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.role.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Role::find($id);
		
		if ($result) {

			\App\Models\Role::where('id', $id)->forceDelete();
			$request->session()->flash('success','Role deleted successfully.');

		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Role::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Role status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
