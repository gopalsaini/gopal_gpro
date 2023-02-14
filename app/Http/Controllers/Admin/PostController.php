<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'title'=>'required',
				'description'=>'required',
			];

            if((int) $request->post('id')==0){
						
				$rules['uploadfile']='required';
			}

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
                    $post=\App\Models\Post::find($request->post('id'));
                } else {
                    $post=new \App\Models\Post();
                }				
                

                $image_array = array();
					$productImage="";

					if(isset($request->uploadfile)){
						foreach($request->uploadfile as $image){

							if($image != 'undefined'){
								$image_update = strtotime(date('Y-m-d H:i:s')).'_'.rand(11,99).'.'.$image->getClientOriginalExtension();
								$image_array[] = $image_update;
								$destinationPath = public_path('/uploads/post');
								$image->move($destinationPath, $image_update);
							}
						}
					}
					
					if(!empty($request->post('images'))){
						
						$image_array=array_merge($request->post('images'),$image_array);
						
					}
					
					if(!empty($image_array) && $image_array[0]!=''){
					
						$productImage = implode(",",$image_array);
					}

                $post->title = $request->post('title');
                $post->description = $request->post('description');
                $post->uploadfile=$productImage;

                $post->save();
                
                if ((int) $request->post('id') == 0) {
                    return response(array('message'=>'Community added successfully.', 'reset'=>true,'script'=>true),200);
                } else {
                    return response(array('message'=>'Community updated successfully.','reset'=>false),200);
                }

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$result = array();
        return view('admin.post.add', compact('result'));

    }

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('posts');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Post::orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Post::count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('title', function($data){
				return $data->title;
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
				$msg = "' Are you sure to delete this post ?'";
				
				return '<a href="'.route('admin.post.edit', ['id' => $data->id] ).'" title="Edit Post" class="btn btn-sm btn-primary px-3"><i class="fas fa-pencil-alt"></i></a>
				<a href="'.route('admin.post.delete', ['id' => $data->id] ).'" title="Delete Post" class="btn btn-sm btn-danger px-3" onclick="return confirm('.$msg.');"><i class="fas fa-trash"></i></a>';
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.post.list');

	}

    public function edit($id) {
		
		$result = \App\Models\Post::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.post.add')->with(compact('result'));

	}

    public function delete(Request $request, $id) {

		$result = \App\Models\Post::find($id);
		
		if ($result) {

			\App\Models\Post::where('id', $id)->delete();
			$request->session()->flash('5fernsadminsuccess','Post deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\Post::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->save();

			return response(array('message'=>'Post status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
}
