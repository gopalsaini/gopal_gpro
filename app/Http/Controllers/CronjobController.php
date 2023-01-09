<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use Mail;
use Validator;
use Newsletter;
use DB;
use Ixudra\Curl\Facades\Curl;

class CronjobController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {  
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	 
    public function index(){

        $result= \App\Models\Setting::join('token_saves', 'token_saves.user_id', '=', 'settings.user_id')->get(['settings.*', 'token_saves.*']);

        if($result->count()>0){

            foreach($result as $value){

                $createSnampShot=false; 

                $takeSnapShotHours=24;
                if($value['snapshot']=='custom'){
                    $takeSnapShotHours=(int) $value['custom'];
                }

                if($value['snapshot']=='once'){
                    $takeSnapShotHours='24';
                }

                if($value['snapshot']=='twice'){
                    $takeSnapShotHours='12';
                }

                if($value['snapshot']=='thrice'){
                    $takeSnapShotHours='8';
                }

                if((24%$takeSnapShotHours==0)){

                    $createSnamoShot=true;
                } 

				
				$data =[
					"type"=>"snapshot",
					"name"=>"Snapshot-".rand(0000,9999),
				];
				 
				$response = Curl::to('https://api.digitalocean.com/v2/droplets/'.$value['dropletid'].'/actions')
                ->withData(json_encode($data))
                ->withBearer($value['token'])
				->returnResponseObject()
				->withHeader('Content-Type: application/json')
                ->post();
				 
				if($response->status==201){
					$data = json_decode($response->content, true);
					$inprocess=new \App\Models\inprocess_snapshot();
					$inprocess->user_id=$value['user_id'];
					$inprocess->dropletid=$value['dropletid'];
					$inprocess->snapshotid=$data['action']['id'];
					$inprocess->save(); 
					
				}else if($response->status==404){
					
					\App\Models\Setting::where('dropletid',$value['dropletid'])->delete();
					
				}
				
				
				//create delete process
				
				$response = Curl::to('https://api.digitalocean.com/v2/droplets/'.$value['dropletid'].'/snapshots') 
                ->withBearer($value['token'])
				->returnResponseObject() 
                ->get();
				
				if($response->status==200){
					
					$deleteIdArray=[];
					$data = json_decode($response->content, true);
					$data=array_reverse($data['snapshots']);
					
					if($value['previous_snapshots']=='Number'){
						
						if(count($data)>$value['day']){
							
							for($i=$value['day'];$i<count($data);$i++){
								
								$deleteIdArray[]=array(
									'id'=>$data[$i]['id'],
									'token'=>$value['token']
								);
							}
							
						}
						
					}
					
					
					if($value['previous_snapshots']=='Day'){
						
						$expireDate=date('Y-m-d H:i:s', strtotime(' - '.$value['day'].' days'));
	
						if(count($data)>1){
							foreach($data as $key=>$value1){
								
								$createdDate=date('Y-m-d H:i:s',strtotime($value1['created_at'])); 
								
								if($createdDate<$expireDate){
									
									$deleteIdArray[]=array(
										'id'=>$value['id'],
										'token'=>$value['token']
									);
								}
							} 
						}
						
					} 
					
					if(!empty($deleteIdArray)){
						
						
						foreach($deleteIdArray as $delete){
							
							Curl::to('https://api.digitalocean.com/v2/snapshots/'.$delete['id']) 
									->withBearer($delete['token'])
									->returnResponseObject() 
									->delete();
							
						}
						
					}
				}  
                
            }
        }
		
		echo 'Done';
		die;
    }
	 
	
}
