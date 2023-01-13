<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FaqController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'category' => 'nullable',
				'question' => ['required', \Illuminate\Validation\Rule::unique('faqs')->where(function ($query) use($request) {
					return $query->where('category', $request->category)->where('id', '!=', $request->id);
				})],
				'answer'=>'required',
				'spanish_question'=>'required',
				'spanish_answer'=>'required',
				'french_question'=>'required',
				'french_answer'=>'required',
				'portuguese_question'=>'required',
				'portuguese_answer'=>'required',
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
                    $data=\App\Models\Faq::find($request->post('id'));
                } else {
                    $data=new \App\Models\Faq();
                }				
                
                $data->category = $request->post('category');
                $data->sp_question = $request->post('spanish_question');
                $data->sp_answer = $request->post('spanish_answer');
                $data->fr_question = $request->post('french_question');
                $data->fr_answer = $request->post('french_answer');
                $data->pt_question = $request->post('portuguese_question');
                $data->pt_answer = $request->post('portuguese_answer');
                $data->question = $request->post('question');
                $data->answer = $request->post('answer');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Faq added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Faq updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
		$faqs = \App\Models\Category::orderBy('name','Asc')->where('status','1')->get();
        return view('admin.faq.add', compact('result', 'faqs'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('faqs');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Faq::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Faq::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('category', function($data){
				return \App\Helpers\commonHelper::getCategoryName($data->category);
		    })

			->addColumn('question', function($data){
				return $data->question;
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

					$msg = "' Are you sure to delete this faq ?'";
				
					return '<a href="'.route('admin.faq.edit', ['id' => $data->id] ).'" title="Edit faq" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
					<a href="'.route('admin.faq.delete', ['id' => $data->id] ).'" title="Delete faq" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
	
				}else{
					return '<a href="'.route('admin.faq.view', ['id' => $data->id] ).'" title="View Faq" class="btn btn-sm btn-primary px-3"><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.faq.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Faq::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		$faqs = \App\Models\Category::orderBy('name','Asc')->where('status','1')->get();

		return view('admin.faq.add')->with(compact('result', 'faqs'));

	}

    public function view($id) {
		
		$result = \App\Models\Faq::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		$faqs = \App\Models\Faq::groupBy('category')->get();

		return view('admin.faq.view')->with(compact('result', 'faqs'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Faq::find($id);
		
		if ($result) {

			\App\Models\Faq::where('id', $id)->delete();
			$request->session()->flash('success','Faq deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Faq::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Faq status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	

	public function helpList(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('messages');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Message::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Message::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name;
		    })
			->addColumn('email', function($data){
				return $data->email;
		    })
			->addColumn('mobile', function($data){
				return $data->mobile;
		    })
			->addColumn('message', function($data){
				return $data->message;
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();
        return view('admin.help.helplist');

	}
}
