<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TestimonialController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'title'=>'nullable',
				'designation'=>'nullable',
				'description'=>'required',

				'sp_title'=>'nullable',
				'sp_designation'=>'nullable',
				'sp_description'=>'required',

				'fr_title'=>'nullable',
				'fr_designation'=>'nullable',
				'fr_description'=>'required',

				'pt_title'=>'nullable',
				'pt_designation'=>'nullable',
				'pt_description'=>'required'
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
                    $data=\App\Models\Testimonial::find($request->post('id'));
                } else {
                    $data=new \App\Models\Testimonial();
                }				
                
                $data->sp_title = $request->post('sp_title');
                $data->sp_designation = $request->post('sp_designation');
                $data->sp_description = $request->post('sp_description');

                $data->fr_title = $request->post('fr_title');
                $data->fr_designation = $request->post('fr_designation');
                $data->fr_description = $request->post('fr_description');
				
                $data->pt_title = $request->post('pt_title');
                $data->pt_designation = $request->post('pt_designation');
                $data->pt_description = $request->post('pt_description');
				
                $data->title = $request->post('title');
                $data->designation = $request->post('designation');
                $data->description = $request->post('description');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Testimonial added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Testimonial updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.testimonial.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('testimonials');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Testimonial::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Testimonial::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('title', function($data){
				return $data->title;
		    })

			->addColumn('designation', function($data){
				return $data->designation;
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
				$msg = "' Are you sure to delete this testimonial ?'";
				
				return '<a href="'.route('admin.testimonial.edit', ['id' => $data->id] ).'" title="Edit testimonial" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.testimonial.delete', ['id' => $data->id] ).'" title="Delete testimonial" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.testimonial.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Testimonial::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.testimonial.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Testimonial::find($id);
		
		if ($result) {

			\App\Models\Testimonial::where('id', $id)->delete();
			$request->session()->flash('success','Testimonial deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Testimonial::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Testimonial status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
