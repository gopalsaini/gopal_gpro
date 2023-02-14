<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class PreRecordedVideo extends Controller
{
    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name'=>'required',
			];

            if((int) $request->post('id')==0){
						
				$rules['video']='required|mimes:mp4';
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
                    $preRecordedVideo=\App\Models\PreRecordedVideo::find($request->post('id'));
                } else {
                    $preRecordedVideo=new \App\Models\PreRecordedVideo();
                }				
                
                if($request->hasFile('video')){
                    $videoData = $request->file('video');
                    $file = strtotime(date('Y-m-d H:i:s')).'.'.$videoData->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/pre-recorded-video');
                    $videoData->move($destinationPath, $file);

                    $preRecordedVideo->video = $file;
                }

                $preRecordedVideo->name = $request->post('name');

                $preRecordedVideo->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'PreRecorded Video added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'PreRecorded Video updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.pre_recorded_video.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('pre_recorded_videos');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\PreRecordedVideo::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\PreRecordedVideo::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name;
		    })

			->addColumn('video', function($data){
				return '<video width="100" height="100" controls> <source src="'.asset('/uploads/pre-recorded-video/'.$data->video).'"> </video>';
                  
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
				$msg = "' Are you sure to delete this PreRecorded Video ?'";
				
				return '<a href="'.route('admin.pre-recorded-video.edit', ['id' => $data->id] ).'" title="Edit PreRecorded Video" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.pre-recorded-video.delete', ['id' => $data->id] ).'" title="Delete PreRecorded Video" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.pre_recorded_video.list');

	}

    public function edit($id) {
		
		$result = \App\Models\PreRecordedVideo::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.pre_recorded_video.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\PreRecordedVideo::find($id);
		
		if ($result) {

			\App\Models\PreRecordedVideo::where('id', $id)->delete();
			$request->session()->flash('5fernsadminsuccess','PreRecorded Video deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\PreRecordedVideo::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'PreRecorded Video status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
}
