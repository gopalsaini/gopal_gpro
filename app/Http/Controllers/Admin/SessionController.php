<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SessionController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name' => ['required', \Illuminate\Validation\Rule::unique('day_sessions')->where(function ($query) use($request) {
					return $query->where('id', '!=', $request->id);
				})],
				'session_name'=>'required',
				'date'=>'required|date',
				'start_time'=>'required',
				'end_time'=>'required',
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
                    $data=\App\Models\DaySession::find($request->post('id'));
                } else {
                    $data=new \App\Models\DaySession();
                }	

                
                $data->name = $request->post('name');
                $data->session_name = $request->post('session_name');
                $data->date = $request->post('date');
                $data->start_time = $request->post('start_time');
                $data->end_time = $request->post('end_time');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Session added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Session updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.day_session.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('day_sessions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\DaySession::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\DaySession::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name;
		    })
			->addColumn('session_date', function($data){
				return $data->session_date;
		    })
			->addColumn('date', function($data){
				return $data->date;
		    })
			->addColumn('start_time', function($data){
				return $data->start_time;
		    })
			->addColumn('end_time', function($data){
				return $data->end_time;
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
				$msg = "' Are you sure to delete this Session ?'";
				
				return '<a href="'.route('admin.session.edit', ['id' => $data->id] ).'" title="Edit Session" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.session.delete', ['id' => $data->id] ).'" title="Delete Session" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.day_session.list');

	}

    public function edit($id) {
		
		$result = \App\Models\DaySession::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.day_session.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\DaySession::find($id);
		
		if ($result) {

			\App\Models\DaySession::where('id', $id)->delete();
			$request->session()->flash('success','Session deleted successfully.');

		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\DaySession::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Session status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
