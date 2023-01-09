<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class NotificationController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				
				'title' => 'required',
				'message'=>'required'
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
            
                if(!empty($request->post('user_id')) && count($request->post('user_id'))>0){

					foreach($request->post('user_id') as $user){

						$data=new \App\Models\Notification();
						$data->user_id = $user;
						$data->title = $request->post('title');
						$data->message = $request->post('message');
						$data->save();
					}
					

				}else{

					return response(array('message'=>'Something went wrong! Please try again.', 'reset'=>false),403);
				}
                
                return response(array('message'=>'Notification send successfully.', 'reset'=>true),200);
                

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
		
        return view('admin.notification.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('notifications');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Notification::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Notification::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('user', function($data){
				return \App\Helpers\commonHelper::getUserNameById($data->user_id);
		    })

			->addColumn('title', function($data){
				return $data->title;
		    })
			->addColumn('message', function($data){
				return $data->message;
		    })


			->addColumn('action', function($data){

				return '<a href="'.route('admin.notification.view', ['id' => $data->id] ).'" title="View notification" class="btn btn-sm btn-primary px-3"><i class="fas fa-eye"></i></a>';
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.notification.list');

	}

    

    public function view(Request $request,$id) {
		
		$result = \App\Models\Notification::find($id);

		if (!$result) {

			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

		return view('admin.notification.view')->with(compact('result'));

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
	
}
