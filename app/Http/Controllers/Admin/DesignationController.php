<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DesignationController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'designations' => ['required', 'string', \Illuminate\Validation\Rule::unique('designations')->where(function ($query) use($request) {
					return $query->where('id', '!=', $request->id);
				})],
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
                    $data=\App\Models\Designation::find($request->post('id'));
                } else {
                    $data=new \App\Models\Designation();
                }
                
                $data->designations = $request->post('designations');
                $data->slug = \Str::slug($request->post('designations'));
                $data->save();
                
                if ((int) $request->post('id') == 0) {
					
                    $setting=new \App\Models\StageSetting();
					$setting->designation_id = $data->id;
					$setting->save();

					$menu=new \App\Models\Menu();
					$menu->label = $data->designations;
					$menu->link = 'admin/user/'.$data->slug.'/stage/zero';
					$menu->active_link = 'admin/user/'.$data->slug.'/*';
					$menu->parent = '2';
					$menu->sort = (\App\Models\Designation::count())-1;
					$menu->save();

					$user_role=new \App\Models\User_role();
					$user_role->designation_id = '1';
					$user_role->menu_id = $menu->id;
					$user_role->save();

                    return response(array('message'=>'Designation added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Designation updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.designation.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('designations');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Designation::where('slug', '!=', 'admin')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Designation::where('slug', '!=', 'admin')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('designation', function($data){
				return $data->designations;
		    })

			->addColumn('status', function($data){
				if($data->status=='1'){ 
					$checked = "checked";
				}else{
					$checked = " ";
				}

				return '<div class="media-body icon-state switch-outline">
							<label class="switch">
								<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
							</label>
						</div>';
		    })

			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this designation ?'";

				// <a href="'.route('admin.designation.delete', ['id' => $data->id] ).'" title="Delete designation" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>
				return '<a href="'.route('admin.designation.edit', ['id' => $data->id] ).'" title="Edit designation" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.designation.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Designation::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.designation.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Designation::find($id);
		
		if ($result) {

			\App\Models\Designation::where('id', $id)->delete();
			$request->session()->flash('success','Designation deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Designation::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Designation changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
