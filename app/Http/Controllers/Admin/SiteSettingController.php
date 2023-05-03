<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			
			$rules=[
				'id'=>'numeric|required',
				'en_title'=>'required',
				'sp_title'=>'required',
				'pt_title'=>'required',
				'fr_title'=>'required',
			];
			 
					
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()){
				$message = "";
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				}
				
				return response(array('message'=>$message),403);
				
			}else{
				
				try{
					if((int) $request->post('id')>0){
						
						$siteSetting=SiteSetting::find($request->post('id'));
					}else{
						
						$siteSetting=new SiteSetting();
					
					}

					$siteSetting->en_title=$request->post('en_title');
					$siteSetting->sp_title=$request->post('sp_title');
					$siteSetting->pt_title=$request->post('pt_title');
					$siteSetting->fr_title=$request->post('fr_title');
					
					$siteSetting->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Site Setting updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'Site Setting added successfully.','reset'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=SiteSetting::first();
		if(!$result){
			$result = array();
			
		}
        return view('admin.site_setting.add',compact('result'));
    }

	public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('site_settings');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\SiteSetting::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\SiteSetting::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('en_title', function($data){
					return $data->en_title;
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

				
					return '<a href="'.route('admin.site-setting.edit', ['id' => $data->id] ).'" title="Edit Site Setting" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>';
				
				}
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.site_setting.list');

	}

	// public function delete(Request $request, $id) {

	// 	$result = \App\Models\SiteSetting::find($id);
		
	// 	if(\Auth::user()->designation_id != 11){
	// 		if ($result) {

	// 			\App\Models\SiteSetting::where('id', $id)->delete();
	// 			$request->session()->flash('success','Site Setting deleted successfully.');
	// 		} else {
	// 			$request->session()->flash('error','Something went wrong. Please try again.');
	// 		}

			
	// 	}else{

	// 		$request->session()->flash('error','Something went wrong. Please try again.');
	// 	}
		
	// 	return redirect()->back();

    // }

	public function status(Request $request) {
		
		$result = \App\Models\SiteSetting::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Site Setting status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function edit($id) {
		
		$result = \App\Models\SiteSetting::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.site_setting.add')->with(compact('result'));

	}
}
