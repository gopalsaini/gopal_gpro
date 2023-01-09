<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SubOfferController extends Controller {

    public function add(Request $request, $offer_id) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'offer_id'=>'required|numeric',
				'name'=>'required|string',
				'initial_amount'=>'required|numeric',
				'final_amount'=>'required|numeric',
				'instant_discount'=>'required|numeric'
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
                    $data=\App\Models\SubOffer::find($request->post('id'));
                } else {
                    $data=new \App\Models\SubOffer();
                }
                
                $data->offer_id = $request->post('offer_id');
                $data->name = $request->post('name');
                $data->initial_amount = $request->post('initial_amount');
                $data->final_amount = $request->post('final_amount');
                $data->instant_discount = $request->post('instant_discount');
                $data->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Sub offer added successfully.', 'reset'=>true),200);
                } else {
                    return response(array('message'=>'Sub offer updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.suboffer.add', compact('result', 'offer_id'));

    }

    public function list(Request $request, $offer_id) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('sub_offers');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\SubOffer::where('offer_id', $offer_id)->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\SubOffer::where('offer_id', $offer_id)->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('offer_code', function($data){
				return $data->offer_id;
		    })

			->addColumn('name', function($data){
				return $data->name;
		    })

			->addColumn('initial_amount', function($data){
				return $data->initial_amount;
		    })

			->addColumn('final_amount', function($data){
				return $data->final_amount;
		    })

			->addColumn('instant_discount', function($data){
				return $data->instant_discount;
		    })

			->addColumn('status', function($data){
				if($data->status=='1'){ 
					$checked = "checked";
				}else{
					$checked = " ";
				}

				return '<div class="media-body icon-state switch-outline">
							<label class="switch">
								<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
							</label>
						</div>';
		    })

			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this sub offer ?'";

				return '<a href="'.route('admin.sub.offer.edit', ['offer_id' => $data->offer_id, 'id' => $data->id] ).'" title="Edit sub offer" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.sub.offer.delete', ['offer_id' => $data->offer_id, 'id' => $data->id] ).'" title="Delete sub offer" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.suboffer.list', compact('offer_id'));

	}

    public function edit($id, $offer_id) {
		
		$result = \App\Models\SubOffer::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.suboffer.add')->with(compact('result', 'offer_id'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\SubOffer::find($id);
		
		if ($result) {

			\App\Models\SubOffer::where('id', $id)->delete();
			$request->session()->flash('success','Sub offer deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\SubOffer::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Sub offer status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
}
