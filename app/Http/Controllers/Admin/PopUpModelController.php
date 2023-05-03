<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PopUpModelController extends Controller
{
    
    public function add(Request $request) {
			
		
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'type'=>'required',
				'expired_date'=>'required|date',
				'en_title'=>'required',
				'en_description'=>'required',
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
                    $data=\App\Models\PopUpModel::find($request->post('id'));
                } else {
                    $data=new \App\Models\PopUpModel();
                }				
                
                $data->type = $request->post('type');
                $data->expired_date = $request->post('expired_date');
                $data->en_title = $request->post('en_title');
                $data->en_description = $request->post('en_description');
                $data->sp_title = $request->post('sp_title');
                $data->sp_description = $request->post('sp_description');
                $data->pt_title = $request->post('pt_title');
                $data->pt_description = $request->post('pt_description');
                $data->fr_title = $request->post('fr_title');
                $data->fr_description = $request->post('fr_description');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Popup Model added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Popup Model updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.popup_model.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('pop_up_models');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\PopUpModel::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\PopUpModel::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('type', function($data){

				if($data->type == '1'){

					return 'Early Bird';

				}elseif($data->type == '2'){

					return 'Passport Info';

				}elseif($data->type == '3'){

					return 'Travel Info';
				}else{
					
					return 'Session Info';
				}
		    })

			->addColumn('status', function($data){

				if(\Auth::user()->designation_id == 1){

					if ($data->status=='Approve') { 
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

					$msg = "' Are you sure to delete this popUp model ?'";
				
					return '<a href="'.route('admin.popup-model.edit', ['id' => $data->id] ).'" title="Edit popUp model" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
					<a href="'.route('admin.popup-model.delete', ['id' => $data->id] ).'" title="Delete information" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
				
				}else{
					return '<a href="'.route('admin.popup-model.view', ['id' => $data->id] ).'" title="View popUp model" class="btn btn-sm btn-primary px-3"><i class="fas fa-eye"></i></a>';
				}
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.popup_model.list');

	}

    public function edit($id) {
		
		$result = \App\Models\PopUpModel::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.popup_model.add')->with(compact('result'));

	}

    public function view($id) {
		
		$result = \App\Models\PopUpModel::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.popup_model.view')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\PopUpModel::find($id);
		
		if(\Auth::user()->designation_id != 11){
			if ($result) {

				\App\Models\PopUpModel::where('id', $id)->delete();
				$request->session()->flash('success','PopUpModel deleted successfully.');
			} else {
				$request->session()->flash('error','Something went wrong. Please try again.');
			}

			
		}else{

			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\PopUpModel::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'PopUpModel status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
}
