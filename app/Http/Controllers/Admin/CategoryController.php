<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'name'=>'required',
				'sp_name'=>'required',
				'fr_name'=>'required',
				'pt_name'=>'required',
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
                    $data=\App\Models\Category::find($request->post('id'));
                } else {
                    $data=new \App\Models\Category();
                }				
                
                $data->name = $request->post('name');
                $data->sp_name = $request->post('sp_name');
                $data->fr_name = $request->post('fr_name');
                $data->pt_name = $request->post('pt_name');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Faq Category added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Faq  Category updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
		
        return view('admin.category.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('categories');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Category::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Category::count();
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

					$msg = "' Are you sure to delete this Category?'";
				
					return '<a href="'.route('admin.category.edit', ['id' => $data->id] ).'" title="Edit category" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
					<a href="'.route('admin.category.delete', ['id' => $data->id] ).'" title="Delete category" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
	
				}else{
					return '<a href="'.route('admin.category.view', ['id' => $data->id] ).'" title="View category" class="btn btn-sm btn-primary px-3"><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.category.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Category::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		
		return view('admin.category.add')->with(compact('result'));

	}

    public function view($id) {
		
		$result = \App\Models\Category::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		
		return view('admin.category.view')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Category::find($id);
		
		if ($result) {

			\App\Models\Category::where('id', $id)->delete();
			$request->session()->flash('success','Category deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Category::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Category status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
