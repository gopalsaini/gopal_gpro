<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class OfferController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				// 'offer_type'=>'required|numeric',
				'name'=>'required|string',
				'code' => ['required', \Illuminate\Validation\Rule::unique('offers')->where(function ($query) use($request) {
					return $query->where('id', '!=', $request->id)->where('deleted_at', NULL);
				})],
				'start_date'=>'required|date',
				'end_date'=>'required|date',
				'discount_type'=>'required|in:1,2',
				'discount_value'=>'required',
				// 'is_partial_amount'=>'required|in:0,1',
				// 'partial_amount'=>'required_if:is_partial_amount,1',
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
                    $data=\App\Models\Offer::find($request->post('id'));
                } else {
                    $data=new \App\Models\Offer();
                }				
                
                $data->offer_type = '1';
                $data->name = $request->post('name');
                $data->code = $request->post('code');
                $data->start_date = $request->post('start_date');
                $data->end_date = $request->post('end_date');
                $data->discount_type = $request->post('discount_type');
                $data->discount_value = $request->post('discount_value');

				// if ($request->post('is_partial_amount') == '1') {
				// 	$data->partial_amount = $request->post('partial_amount');
				// } else {
				// 	$data->partial_amount = '0.00';
				// }

				// $data->is_partial_amount = $request->post('is_partial_amount');

                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Offer added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Offer updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.offer.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('offers');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Offer::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Offer::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			// ->addColumn('offer_type', function($data){
			// 	return $data->offer_type;
		    // })

			->addColumn('name', function($data){
				return $data->name;
		    })

			->addColumn('code', function($data){
				return $data->code;
		    })

			->addColumn('start_date', function($data){
				return date('Y-m-d', strtotime($data->start_date));
		    })

			->addColumn('end_date', function($data){
				return date('Y-m-d', strtotime($data->end_date));
		    })

			->addColumn('discount_type', function($data){
				if ($data->discount_type == '1') { 
					return '%';
				}else if ($data->discount_type == '2') { 
					return 'Flat';
				}
		    })

			->addColumn('discount_value', function($data){
				return $data->discount_value;
		    })

			// ->addColumn('is_partial_amount', function($data){
			// 	if ($data->is_partial_amount == '1') {
			// 		return 'Yes';
			// 	} else {
			// 		return 'No';
			// 	}
		    // })

			// ->addColumn('partial_amount', function($data){
			// 	if ($data->is_partial_amount == '1') {
			// 		return $data->partial_amount;
			// 	} else {
			// 		return '-';
			// 	}
		    // })

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
					
					$msg = "' Are you sure to delete this offer ?'";

					// <a href="'.route('admin.sub.offer.add', ['offer_id' => $data->id] ).'" title="Add sub offer" class="btn btn-sm btn-warning px-3"><i class="fas fa-plus"></i></a>
					// <a href="'.route('admin.sub.offer.list', ['offer_id' => $data->id] ).'" title="Sub offer list" class="btn btn-sm btn-dark px-3"><i class="fas fa-list"></i></a>
					
					return '<a href="'.route('admin.offer.edit', ['id' => $data->id] ).'" title="Edit offer" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
					<a href="'.route('admin.offer.delete', ['id' => $data->id] ).'" title="Delete offer" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
	
				}else{
					return '-';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.offer.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Offer::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.offer.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Offer::find($id);
		
		if ($result) {

			\App\Models\Offer::where('id', $id)->delete();
			$request->session()->flash('success','Offer deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Offer::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Offer status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
