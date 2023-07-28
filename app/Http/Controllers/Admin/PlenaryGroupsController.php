<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class PlenaryGroupsController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'name' => 'string|required',
				'users' => 'required|array',
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

				if(count($request->post('users'))<10){
					return response(array('message'=>'Minimum 10 members in a Group Required'),403);
				}

				if(count($request->post('users'))>10){
					return response(array('message'=>'Maximum 10 members in a Group Required'),403);
				}

				$members = [];

				foreach($request->post('users') as $key=>$user){

					if($key < 2){

						$members[] = [
							'user'=>$user,
							'role'=>'Leader',
						];

					}else{
						$members[] = [
							'user'=>$user,
							'role'=>'Member',
						];
					}
					
				}

               
                $data=new \App\Models\PlenaryGroups();
                
				$data->name = $request->post('name');
				$data->member = json_encode($members);
				$data->user = implode(',',$request->post('users'));
				$data->save();
                
                return response(array('message'=>'Plenary Groups added successfully.', 'reset'=>true),200);
               

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();

		$users = \App\Models\User::where([['id', '!=', '1'],['name','!=', ''],['stage','=', '3']])->orderBy('name', 'asc')->get();

        return view('admin.plenary_groups.add', compact('result','users'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('plenary_groups');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\PlenaryGroups::orderBy($order,$dir);

			if (request()->has('email')) {
				$query->where('name', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\PlenaryGroups::orderBy($order,$dir);

			if (request()->has('email')) {
				
				$totalData1->where('name', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return '<a href="javascript:void(0)" class="group-user-list" data-email="'.$data->id.'"></a> '.$data->name;
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


		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.plenary_groups.list');

	}

    public function edit(Request $request,$id) {
		
		$result = \App\Models\PlenaryGroups::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.plenary_groups.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\PlenaryGroups::find($id);
		
		if ($result) {

			\App\Models\PlenaryGroups::where('id', $id)->forceDelete();
			$request->session()->flash('success','Plenary Groups deleted successfully.');

		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\PlenaryGroups::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Plenary Groups status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	
	public function getGroupUsersList(Request $request) {

		$data = \App\Models\PlenaryGroups::where('id', $request->post('id'))->first();
		
		$html = '<table cellpadding="" cellspacing="0" border="0" style="width: 100%;"> 
					<thead> 
					<tr> 
					<th class="text-center">S.N.</th> 
					<th class="text-center">Name</th> 
					<th class="text-center">Email</th> 
					<th class="text-center">Role</th>';
		
		if ($data) {

			$dataUser = json_decode($data->member, true);
			foreach ($dataUser as $key=>$result) {

				$user = \App\Models\User::where('id', $result['user'])->first();
				
				$key += 1;
				$html .= '<tr>';
				$html .= '<td class="text-center">'.$key.'.</td>';

				$html .= '<td class="text-center">'.$user->name.' '.$user->last_name;
				$html .= '</td>';

				$html .= '<td class="text-center">'.$user->email;
				$html .= '</td>';

				$html .= '<td class="text-center">'.$result['role'];
				$html .= '</td>';

				$html .= '</tr>';
			}
		} else {
			$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
		}
		$html .= '</tbody></table>';

		return response()->json(array('html'=>$html));
		

	}

	
}
