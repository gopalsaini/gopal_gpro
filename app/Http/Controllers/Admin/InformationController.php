<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class InformationController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'title' => ['required', \Illuminate\Validation\Rule::unique('informations')->where(function ($query) use($request) {
					return $query->where('id', '!=', $request->id);
				})],
				'description'=>'required',
				'sp_title'=>'required',
				'sp_description'=>'required',
				'pt_title'=>'required',
				'pt_description'=>'required',
				'fr_title'=>'required',
				'fr_description'=>'required',
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
                    $data=\App\Models\Information::find($request->post('id'));
                } else {
                    $data=new \App\Models\Information();
                }				
                
                $data->title = $request->post('title');
                $data->slug = \Str::slug($request->post('title'));
                $data->description = $request->post('description');
                $data->sp_title = $request->post('sp_title');
                $data->sp_description = $request->post('sp_description');
                $data->pt_title = $request->post('pt_title');
                $data->pt_description = $request->post('pt_description');
                $data->fr_title = $request->post('fr_title');
                $data->fr_description = $request->post('fr_description');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Information added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Information updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.information.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('informations');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Information::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Information::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('title', function($data){
				return $data->title;
		    })

			->addColumn('status', function($data){

				if(\Auth::user()->designation_id == 1){

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
				}else{
					return '-';
				}
				
		    })

			->addColumn('action', function($data){

				if(\Auth::user()->designation_id == 1){

					$msg = "' Are you sure to delete this information ?'";
				
					return '<a href="'.route('admin.information.edit', ['id' => $data->id] ).'" title="Edit information" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
					<a href="'.route('admin.information.delete', ['id' => $data->id] ).'" title="Delete information" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
				
				}else{
					return '<a href="'.route('admin.information.view', ['id' => $data->id] ).'" title="View information" class="btn btn-sm btn-primary px-3"><i class="fas fa-eye"></i></a>';
				}
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.information.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Information::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.information.add')->with(compact('result'));

	}

    public function view($id) {
		
		$result = \App\Models\Information::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.information.view')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Information::find($id);
		
		if(\Auth::user()->designation_id != 11){
			if ($result) {

				\App\Models\Information::where('id', $id)->delete();
				$request->session()->flash('success','Information deleted successfully.');
			} else {
				$request->session()->flash('error','Something went wrong. Please try again.');
			}

			
		}else{

			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Information::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Information status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
