<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name'=>'required',
				'description'=>'required',
			];

            if((int) $request->post('id')==0){
						
				$rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:width=350,height=352';
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
                    $speaker=\App\Models\Speaker::find($request->post('id'));
                } else {
                    $speaker=new \App\Models\Speaker();
                }				
                
                if($request->hasFile('image')){
                    $imageData = $request->file('image');
                    $file = strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/speaker');
                    $imageData->move($destinationPath, $file);

                    $speaker->image = $file;
                }

                $speaker->name = $request->post('name');
                $speaker->description = $request->post('description');

                $speaker->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Speaker added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Speaker updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.speaker.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('speakers');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Speaker::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Speaker::count();
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
				return '<img src="'.asset('/uploads/speaker/'.$data->image).'" style="width: 50px;border:1px solid #222;margin-right: 13px"/>';
                
                
		    })

			->addColumn('description', function($data){
				return $data->description;
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
				$msg = "' Are you sure to delete this speaker ?'";
				
				return '<a href="'.route('admin.speaker.edit', ['id' => $data->id] ).'" title="Edit Speaker" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.speaker.delete', ['id' => $data->id] ).'" title="Delete Speaker" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.speaker.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Speaker::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.speaker.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Speaker::find($id);
		
		if ($result) {

			\App\Models\Speaker::where('id', $id)->delete();
			$request->session()->flash('5fernsadminsuccess','Speaker deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Speaker::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Speaker status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
}
