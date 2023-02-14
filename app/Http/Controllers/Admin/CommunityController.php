<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name'=>'required',
			];

            if((int) $request->post('id')==0){
						
				$rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg';
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
            
                if ((int) $request->post('id') > 0) {
                    $community=\App\Models\Community::find($request->post('id'));
                } else {
                    $community=new \App\Models\Community();
                }				
                
                if($request->hasFile('image')){
                    $imageData = $request->file('image');
                    $file = strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/community');
                    $imageData->move($destinationPath, $file);

                    $community->image = $file;
                }

                $community->name = $request->post('name');

                $community->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Community added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Community updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.community.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('communities');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Community::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Community::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name;
		    })

			->addColumn('image', function($data){
				return '<img src="'.asset('/uploads/community/'.$data->image).'" style="width: 50px;border:1px solid #222;margin-right: 13px"/>';
                
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
				$msg = "' Are you sure to delete this community ?'";
				
				return '<a href="'.route('admin.community.edit', ['id' => $data->id] ).'" title="Edit Community" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.community.delete', ['id' => $data->id] ).'" title="Delete Community" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.community.list');

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
}
