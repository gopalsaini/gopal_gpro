<?php
namespace App\Helpers;
use Ixudra\Curl\Facades\Curl;
use Session;
use DB;

use Stripe;

class commonHelper{

	public static function setLocale(){
        if (\Session::has('lang')) {
            \App::setLocale(\Session::get('lang'));
        }
	}
	
	public static function callAPI($method, $url, $data=array(),$files=array()){
 
		$url=env('APP_URL').'/api'.$url;
 
        if($method == 'GET'){

            return $response = Curl::to($url)
			->returnResponseObject()
            ->get();

        }elseif($method == 'PUT'){

            return $response = Curl::to($url)

            ->withData(['title'=>'Test', 'body'=>'body goes here', 'userId'=>1])
			->returnResponseObject()
            ->put();

        }elseif($method == 'DELETE'){

            return $response = Curl::to($url)

                ->delete();
        }elseif($method == 'patch'){

            return $response = Curl::to($url)

                ->withData(['title'=>'Test', 'body'=>'body goes here', 'userId'=>1])
				->returnResponseObject()
                ->patch();
        }elseif($method == 'POST'){

            return $response = Curl::to($url)
                ->withData($data)
				->returnResponseObject()
                ->post();
                
        }elseif($method == 'POSTFILE'){
			
            return $response = Curl::to($url)
                ->withData($data)
				->withFile($files['file_input'],$files['image_file'], $files['getMimeType'], $files['getClientOriginalName']) 
                ->post();
                
        }elseif($method == 'userTokenpost'){ 
            return $response = Curl::to($url)
                ->withData($data)
                ->withBearer(Session::get('gpro_user'))
				->returnResponseObject()
				->withHeader('Content-Type: application/json')
                ->post();
                
        }elseif($method == 'userTokenget'){
            return $response = Curl::to($url)
            ->withBearer(Session::get('gpro_user'))
			->returnResponseObject()
            ->get();
        }elseif($method == 'userTokendelete'){
            return $response = Curl::to($url)
            ->withBearer(Session::get('gpro_user'))
			->returnResponseObject()
            ->delete();
        }
        
    }

	public static function buildMenu($parent, $menu, $sub = NULL) {

        $html = "";

        if (isset($menu['parents'][$parent])){
            if (!empty($sub)) {
                $html .= "<ul id=" . $sub . " class='ml-menu'><li class=\"ml-menu\">" . $sub . "</li>\n";
            } else {
                $html .= "<ul class='list'>\n";
            }

            foreach ($menu['parents'][$parent] as $itemId) {
                
				$active=(request()->is($menu['items'][$itemId]['active_link'])) ? 'active' :'';

				$terget = null;
                if (!isset($menu['parents'][$itemId])) { //if condition is false only view menu
                    $html.= "<li class='".$active."' >\n  <a $terget title='" . $menu['items'][$itemId]['label'] . "' href='" . url($menu['items'][$itemId]['link']) . "'>\n <em class='" . $menu['items'][$itemId]['icon'] . " fa-fw'></em><span>" . $menu['items'][$itemId]['label'] . "</span></a>\n</li> \n";
				}
				
                if (isset($menu['parents'][$itemId])) { //if condition is true show with submenu
                    $html .= "<li class='" . $active . "'>\n  <a onclick='return false;' class='menu-toggle' href='#" . $menu['items'][$itemId]['label'] . "'> <em class='" . $menu['items'][$itemId]['icon'] . " fa-fw'></em><span>" . $menu['items'][$itemId]['label'] . "</span></a>\n";
                    $html .= self::buildMenu($itemId, $menu, $menu['items'][$itemId]['label']);
                    $html .= "</li> \n";
                }
				
            }
            $html .= "</ul> \n";
			
        }

        return $html;

    }

	public static function getDataById($model_name, $id, $column_name = ''){
		
		$model_name = 'App\Models\\'.$model_name;

		$result = $model_name::find($id);
		
		if ($result) {

			if(empty($column_name)){
				return $result;
			}else{
				return ucfirst($result->$column_name);
			}

		} else {
			return 'N/A';
		}

	}

	public static function getSidebarMenu(){
		
		if(Session::has('fivefernsadminmenu')){

			$result=Session::get('fivefernsadminmenu');

			$menu = array(
				'items' => array(),
				'parents' => array()
			);
	
			foreach ($result as $v_menu) {
				$menu['items'][$v_menu['menu_id']] = $v_menu;
				$menu['parents'][$v_menu['parent']][] = $v_menu['menu_id'];
			}
	
			return  \App\Helpers\commonHelper::buildMenu(0, $menu);
		}

	}

	public static function getSubcategoryById($id){
		
		return \App\Models\Category::where('parent_id',$id)->where('status','1')->where('recyclebin_status','0')->get()->toArray();
	}
	
    public static function getCategoryTree($id){
		
		$category=[];
		
		$categoryResult=\App\Helpers\commonHelper::getSubcategoryById($id);
		
		if($categoryResult){
			
			foreach($categoryResult as $element){
					
				$childResult=\App\Helpers\commonHelper::getCategoryTree($element['id']);

				if($childResult){
					
					$element['child']=$childResult;
				}
				
				$category[]=$element;
			}
		}
		
		return $category;
	}
	
	public static function getCategoryTreeids($id){
		
		$ids="";
		
		$categoryResult=\App\Helpers\commonHelper::getSubcategoryById($id);
		
		if($categoryResult){
			
			foreach($categoryResult as $element){
					
				$childResult=\App\Helpers\commonHelper::getCategoryTreeids($element['id']);

				if($childResult){
					
					$ids.=$childResult;

				}

				$ids.=$element['id'].',';
			}
		}
		
		return $ids;
	}

	public static function getCategoryTreeidsArray($id){

		$idsResult=\App\Helpers\commonHelper::getCategoryTreeids($id);
		
		$idArray=array();

		if(rtrim($idsResult,' , ')){

			$idArray=explode(',',rtrim($idsResult,' , '));
		}

		return $idArray;
		
	}

	public static function getAllCategoryTreeids($id){
		
		$ids="";
		
		$categoryResult=\App\Models\Category::where('parent_id',$id)->where('recyclebin_status','0')->get()->toArray();
		
		if($categoryResult){
			
			foreach($categoryResult as $element){
					
				$childResult=\App\Helpers\commonHelper::getAllCategoryTreeids($element['id']);

				if($childResult){
					
					$ids.=$childResult;

				}

				$ids.=$element['id'].',';
			}
		}
		
		return $ids;
	}

	public static function getAllCategoryTreeidsArray($id){

		$idsResult=\App\Helpers\commonHelper::getAllCategoryTreeids($id);
		
		$idArray=array();

		if(rtrim($idsResult,' , ')){

			$idArray=explode(',',rtrim($idsResult,' , '));
		}

		return $idArray;
		
	}
	
	public static function getCategoryTreeById($id){
		
		$name='';
		
		$result=\App\Models\Category::where('id',$id)->first();
		
		if(!empty($result)){
			
			if($result->parent_id>0){
				
				$name.=\App\Helpers\commonHelper::getCategoryTreeById($result->parent_id);
			}
			
			$name.=ucfirst($result->name).' > ';
			
		}
		
		return $name;
	}
	
	public static function getParentName($id){
		
		$nameResult=\App\Helpers\commonHelper::getCategoryTreeById($id);
		
		return rtrim($nameResult,' > ');
		
	}

	public static function getParentCategoryTreeId($id){
		
		$ids='';
		
		$result=\App\Models\Category::where('id',$id)->first();
		
		if(!empty($result)){
			
			if($result->parent_id>0){
				
				$ids.=\App\Helpers\commonHelper::getParentCategoryTreeId($result->parent_id);
			}
			
			$ids.=$result->id.',';
			
		}
		
		return $ids;
	}
	
	public static function getParentId($id){
		
		$idResult=\App\Helpers\commonHelper::getParentCategoryTreeId($id);

		return rtrim($idResult,',');
	
	}
	
	
	public static function getAttributeByparentId($id){

		return \App\Models\Variant_attribute::where('variant_id',$id)->where('status','1')->orderBy('sort_order','ASC')->get();
		
	}
	
	public static function getOtp(){
		
        $otp = mt_rand(1000,9999);
        // $otp = 1111;

        return $otp;
	}
	
	public static function sendMsg($url){
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
	
	public static function getOrderStatusName($id){
		
		$data=array(
			'0'=>'Failed',
			'1'=>'Pending',
			'2'=>'Confirmed',
			'3'=>'Cancelled',
			'4'=>'Order cancel by user',
			'5'=>'Rejected',
			'6'=>'Cancel request pending',
			'7'=>'Rejected',
			'8'=>'Order Returned',
			'9'=>'Delivered',
			'10'=>'Shipped'
		);
		
		return $data[$id];
	}
	
	
	public static function getPaymentStatusName($id){
		
		$data=array(
			'0'=>'Payment Unpaid',
			'1'=>'Failed',
			'2'=>'Payment Paid',
			'3'=>'Refund Initiated',
			'4'=>'Refund In Progress',
			'5'=>'Refund Completed',
			'6'=>'Payment Initiated',
			'7'=>'Payment Failed',
			'8'=>'Refund Failed',
			'9'=>'Payment Decline',
		);
		
		return $data[$id];
	}
	
	
	public static function getStateNameById($id){
		
		$result=\App\Models\State::where('id',$id)->first();
		
		if($result){
			return ucfirst($result->name);
		}else{
			return "N/A";
		}
	}
	
	
	public static function getCityNameById($id){
		
		$result=\App\Models\City::where('id',$id)->first();
		
		if($result){

			return ucfirst($result->name);

		}else{

			return 'N/A';

		}
		
	}
	
	public static function getCountryidByStateId($id){
		
		$result=\App\Models\State::where('id',$id)->first();
		
		return $result->country_id;
	}
	
	public static function getCountryNameById($id){
		
		$result=\App\Models\Country::where('id',$id)->first();
		if($result){
			return ucfirst($result->name);
		}else{
			return "N/A";
		}
		
	}
	
	public static function getOfferProductPrice($salePrice,$discountType,$discountAmount){

		$discountAmount=$discountAmount;
		if($discountType=='1'){
			
			$discountAmount=round((($salePrice*$discountAmount)/100),2);
		}
		
		return $salePrice-$discountAmount;
	}

	public static function movecartDataWithUser(){

        if(Session::has('5ferns_cartuser') && Session::has('5ferns_user')){

			$cartData=Session::get('5ferns_cartuser');

			if(!empty($cartData)){

				foreach($cartData as $cart){

					\App\Helpers\commonHelper::callAPI('userTokenpost','/add-cart',json_encode(array('id'=>'0','product_id'=>$cart['product_id'],'qty'=>$cart['qty'],'remark'=>$cart['remark'],'add_type'=>'add')));

				}

				Session::forget('5ferns_cartuser');
			}

		}
    }

	public static function getShippingAmount($length,$breadth,$height,$weight,$countryId='101'){

		$dimansionWeight=($length*$breadth*$height)/4000;

		$actualWeight=max($dimansionWeight,$weight);

		$pointValue=ceil($actualWeight/500);

		if((int) $countryId==0){
			
			return "0";

		}else if($countryId=='101'){

			$settingResult=\App\Models\Setting::where('id','1')->first();

		}else{

			$settingResult=\App\Models\Setting::where('id','3')->first();
		}
		
		return $pointValue*(float) $settingResult->value;
	}

	public static function convert_number_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return ucfirst($string);
    }

	public static function getVaraintName($variantIds,$variantAttributes){

		$attribute='';
		$variantArray=explode(',',$variantIds);
												
		if(!empty($variantArray) && $variantArray[0]!=''){
			
			$variantResult=\App\Models\Variant::whereIn('id',$variantArray)->where('status','1')->get();
			
			if(!empty($variantResult)){
					
				foreach($variantResult as $variant){
						
					$attributeArray=explode(',',$variantAttributes); 
					
					if(!empty($attributeArray) && $attributeArray[0]!=''){
							$attributeResult=\App\Models\Variant_attribute::whereIn('id',$attributeArray)->where('variant_id',$variant['id'])->where('status','1')->first();
							
						if($attributeResult){
							
							$attribute.='<label class="labelbold">'.$variant['name'].'</label>:  '.$attributeResult['title'].', ';
						}
						
					} 
				}
			}
		}
		
		return rtrim($attribute,' ,');
	}

	public static function getPriceByCountry($price){

		$countryId='1';

		if(Session::has('country_id')){

			$countryId=Session::get('country_id');
		}

		$result=\App\Models\Currency_value::where('id',$countryId)->first();

		$firstIcon='Rs.';
		$SecondIcon='';
		if($result){

			$price=round(($price*$result['value']),2);
			$firstIcon=$result['first_icon'];
			$SecondIcon=$result['second_icon'];
		}

		return $firstIcon.' '.number_format($price,2).' '.$SecondIcon;

	}

	public static function getPriceAmountByCountryId($price,$countryId){


		$result=\App\Models\Currency_value::where('id',$countryId)->first();

		if($result){

			$price=round(($price*$result['value']),2);
		}

		return $price;

	}

	public static function getpriceIconByCountry($price,$countryId){
		
		$result=\App\Models\Currency_value::where('id',$countryId)->first();
		
		if($result){
			
			$firstIcon=$result['first_icon'];
			$SecondIcon=$result['second_icon']; 
			
			return $firstIcon.' '.number_format($price,2).' '.$SecondIcon;
		}
		
	}

	public static function checkCouponCode($userId,$couponCode,$orderAmount){


		$couponResult=\App\Models\Coupon::where([
			['coupon_code','=',$couponCode],
			['recyclebin_status','=','0'],
			['status','=','1'],
			['start_date','<=',date('Y-m-d')],
			['end_date','>=',date('Y-m-d')]
			])->first();

		if(!$couponResult){

			return ['message'=>'Invalid Coupon code','status'=>'403'];

		}else if($couponResult && $orderAmount<$couponResult['minorder_amount']){

			return ['message'=>"Order amount must be greater than to ".$couponResult['minorder_amount'],'status'=>'403'];

		}else{

			$totalUsesCoupon=\App\Models\Sales::where([
							['sales.user_id',$userId],
							['sales.couponcode_id',$couponResult->id],
							['sales_details.order_status','!=','0']
							])->join('sales_details','sales_details.sale_id','=','sales.id')->groupBy('sales.order_id')->count();

			if($totalUsesCoupon>=$couponResult['totalno_uses']){

				return ['message'=>"This coupon limit has been exceed.",'status'=>'403'];

			}else{

				return ['message'=>"Coupon Code Applied Successfully.","coupon_id"=>$couponResult->id,"discount_type"=>$couponResult->discount_type,"discount_amount"=>$couponResult['discount_amount'],'status'=>'200']; 

			}

		}
	}

	public static function emailSendToUser($to, $subject, $msg, $template = false, $result = false, $pdf = false, $attachment = false){
		// $to = 'deepeshjangid.img@gmail.com';

		if (!$template) {
			$template = 'mail';
		}
		if (!$result) {
			$result = array();
		}

		\Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject, $pdf,$attachment) {
			$message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
			$message->subject($subject);
			$message->to($to);
			if ($pdf) {
				$message->attachData($pdf->output(), "travel_info.pdf");
			}
			
			if ($attachment) {
				$message->attach($attachment);
			}
		});
	}

	public static function emailSendToAdmin($subject, $msg, $template = false, $result = false){

		if (!$template) {
			$template = 'mail';
		}
		if (!$result) {
			$result = array();
		}

		// $admins = \App\Models\User::where('status','1')->where('user_type','1')
		// ->where(function ($query) {
		// 	$query->where('designation_id','1')
		// 		->orWhere('designation_id','12');
		// })->where('id','!=','1')->get();

		// if (!empty($admins) && count($admins)>0) {

		// 	foreach($admins as $admin){

		// 		$to = $admin->email;
		// 		\Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
		// 			$message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
		// 			$message->subject($subject);
		// 			$message->to($to);
		// 		});
		// 	}
			
		// }

			$admins = ['ricardo@gprocongress.org','rania@gprocongress.org',env('ADMIN_EMAIL')];

			foreach($admins as $admin){

				$to = $admin;
				\Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
					$message->from(env('MAIL_USERNAME'), 'GPro');
					$message->subject($subject);
					$message->to($to);
				});
			}

		// $to = env('ADMIN_EMAIL');
		// \Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
		// 	$message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
		// 	$message->subject($subject);
		// 	$message->to($to);
		// });
	}

	public static function getTotalPendingAmount($user_id, $number_format = false){

		$user = \App\Models\User::where('id', $user_id)->first();
		$totalAmount = $user->amount;

		$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user_id);

		$totalPayAmount = $totalAcceptedAmount;

		$totalpendingAmount = 0.00;
		if ($user->amount >= $totalPayAmount) {
			$totalpendingAmount = $user->amount - $totalPayAmount;
		}

		if ($number_format) {
			return number_format($totalpendingAmount, 2);
		} else {
			return $totalpendingAmount;
		}
		
	}

	public static function getTotalAcceptedAmount($user_id, $number_format = false){

		$amount = \App\Models\Wallet::select('wallets.*')
		->join('transactions','transactions.id','=','wallets.transaction_id')
		->where([
				['wallets.status','=','Success'],
				['wallets.type','=','Cr'],
				['wallets.user_id','=',$user_id],
				['transactions.particular_id','!=','3'],
			])->sum('wallets.amount');
		

		if ($number_format) {
			return number_format($amount, 2);
		} else {
			return $amount;
		}

	}
	
	public static function getTotalAmountInProcess($user_id, $number_format = false){

		$amount = \App\Models\Transaction::where([['user_id', '=', $user_id], ['payment_status', '=', '0'], ['particular_id', '!=', '3']])->sum('amount');

		if ($number_format) {
			return number_format($amount, 2);
		} else {
			return $amount;
		}
		
	}
	
	public static function getTotalRejectedAmount($user_id, $number_format = false){
		
		$amount = \App\Models\Wallet::select('wallets.*')
		->join('transactions','transactions.id','=','wallets.transaction_id')
		->where([
				['wallets.status','=','Failed'],
				['wallets.type','=','Cr'],
				['wallets.user_id','=',$user_id],
				['transactions.particular_id','!=','3'],
			])->sum('wallets.amount');

		if ($number_format) {
			return number_format($amount, 2);
		} else {
			return $amount;
		}

	}

	public static function getDesignationId($designation){

		$result = \App\Models\Designation::where('designations', $designation)->first();
		
		if ($result) {
			return $result->id;
		} else {
			return 'N/A';
		}

	}

	public static function getTotalUsersByStage($stage='', $designation='',$profile_status=''){

        $query = \App\Models\User::where([['user_type', '!=', '1'], ['parent_id', NULL], ['added_as', NULL]]);

		if($stage == '0') {
			$query->where('stage', $stage);
		}
		if($stage) {
			$query->where('stage', $stage);
		}

		if($designation) {
			$query->where('designation_id', $designation);
		}

		if($profile_status) {
			$query->where('profile_status', $profile_status);
		}

        $result = $query->count();
		
		if ($result) {
			return $result;
		} else {
			return '0';
		}

	}

	public static function checkGroupUsers($email = false) {

		if ($email) {

			$id = \App\Models\User::where('email', $email)->first()->id;
			$results = \App\Models\User::where([['user_type', '!=', '1'], ['parent_id', $id]])->get();
			if (count($results) > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public static function getStateStatus($stage = '0', $currentstage = '0') {

		if ($stage > $currentstage) {
			return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
		} else if($stage == $currentstage) {
			return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
		} else if($stage < $currentstage) {
			return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
		}

	}

	public static function getBasePriceOfUnmarried($room,$base_price) {

		$category = [];

		if($room == 'Sharing'){
			$category = ['Upgrade to Single Deluxe Room'=>400,'Upgrade to Club Floor'=>700,'Upgrade to Suite'=>1000,'Day pass'=>1000];
			$basePrice = $base_price;


		}else{

			$category = ['Twin Sharing Deluxe Room'=>$base_price,'Upgrade to Club Floor'=>300,'Upgrade to Suite'=>600,'Day pass'=>600];
			$basePrice = $base_price+400;

			
		}

		return ['basePrice'=>$basePrice,'category'=>$category];

	}

	public static function getBasePriceOfMarriedWOSpouse($room,$base_price) {

		$category = [];

		if($room == 'Sharing'){

			$basePrice = $base_price;
			$category = ['Upgrade to Single Deluxe Room'=>400,'Upgrade to Club Floor'=>700,'Upgrade to Suite'=>1000,'Day pass'=>1000];
			$basePrice = $base_price;
			
		}else{

			$category = ['Twin Sharing Deluxe Room'=>$base_price,'Upgrade to Club Floor'=>300,'Upgrade to Suite'=>600,'Day pass'=>600];
			$basePrice = $base_price+400;
			
		}

		return ['basePrice'=>$basePrice,'category'=>$category];

	}

	public static function getBasePriceOfMarriedWSpouse($ministry_pastor_trainer,$SpouseMinistry_pastor_trainer,$base_price) {
			
		$category = [];

		if($ministry_pastor_trainer == 'Yes' && $SpouseMinistry_pastor_trainer == 'Yes'){

			$category = ['Upgrade to Club Floor'=>600,'Upgrade to Suite'=>900,'Day pass'=>1000];
			$basePrice = $base_price*2;
			$trainer = 'Yes';

		}else{

			$category = ['Upgrade to Club Floor'=>700,'Upgrade to Suite'=>1000,'Day pass'=>1000];
			$basePrice = $base_price+1250;
			$trainer = 'No';

		}

		return ['basePrice'=>$basePrice,'category'=>$category,'trainer'=>$trainer];

	}

	public static function getOfferDiscount($id,$amount) {
		
		$Offers = \App\Models\Offer::where('status','1')->where('id',$id)->first();
		$amount = $amount;
		if($Offers){

			if($Offers->discount_type == '1'){

				$amount = $amount-(($amount*$Offers->discount_value)/(100));

			}else{
				
				$amount = $amount-($Offers->discount_value);

			}

		}

		return $amount;

	}

	public static function sendPaymentReminderMailSend($id,$email,$name) {
		
		$result = \App\Models\User::where('id',$id)->first();


		$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($id, true);
		$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($id, true);
		$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($id, true);
		$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);
		
		$to = $email;
		
		if($result->language == 'sp'){

			$subject = "PENDIENTE: Pago de saldo para GProCongress II";
			$msg = '<p>Estimado '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a más tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo estará encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p><div><br></div>';
		
		}elseif($result->language == 'fr'){
		
			$subject = "Paiement du solde GProCongrès II: EN ATTENTE";
			$msg = "<p>Cher ".$result->name." ".$result->last_name.",&nbsp;</p><p><br></p><p>Nous vous écrivons pour vous rappeler que vous avez des paiements en attente pour régler le solde dû sur votre compte GProCongrès II.&nbsp;&nbsp;</p><p>Voici un résumé de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS EFFECTUÉS ANTÉRIEUREMENT ET ACCEPTÉS : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p><p>SOLDE RESTANT DÛ : ".$totalPendingAmount."</p><p><br></p><p>Veuillez payer le solde au plus tard le&nbsp; 31st August 2023.&nbsp;</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous des questions ? Répondez simplement à cet e-mail et notre équipe sera heureuse d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier le nombre et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><div><br></div>";

		}elseif($result->language == 'pt'){
		
			$subject = "Pagamento do Saldo PENDENTE para o II CongressoGPro";
			$msg = '<p>Prezado '.$result->name.'  '.$result->last_name.',&nbsp;</p><p><br></p><p>Estamos escrevendo para lhe lembrar que tem pagamentos pendentes para regularizar o seu saldo em dívida na sua conta para o II CongressoGPro.&nbsp;&nbsp;</p><p><br></p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITO : '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM ABERTO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor pague o saldo até o dia ou antes de 31st August 2023.</p><p><br></p><p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Alguma dúvida? Simplesmente responda a este e-mail, e nossa equipe estará muito feliz para entrar em contacto com você.&nbsp;</p><p><br></p><p>Ore conosco a medida em que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';
		
		}else{
		
			$subject = 'PENDING: Balance payment for GProCongress II';
			$msg = '<div>Dear '.$name.',&nbsp;</div><div><br></div><div>We are writing to remind you that you have pending payments to settle the balance due on your GProCongress II account.&nbsp;&nbsp;</div><div><br></div><div>Here is a summary of your payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID:</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
			
		}

		\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

	}

	public static function paymentGateway($id,$amount = '',$particular = '1') {
		
		
		$user = \App\Models\User::where('id',$id)->first();

		if($amount == ''){

			$amount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);

		}
		
		$transactionId=strtotime("now").rand(11,99);

		$orderId=strtotime("now").rand(11,99);

		$payment=new \App\Models\Transaction();

		$payment->user_id=$user->id ?? null;
		$payment->order_id=$orderId;
		$payment->payment_by='1';
		$payment->transaction_id=$transactionId;
		$payment->amount=$amount;
		$payment->payment_status='0';
		$payment->bank='Card';
		$payment->method='Online';
		$payment->particular_id = $particular;
		$payment->save();

		$Wallet = new \App\Models\Wallet();
		$Wallet->user_id = $user->id;
		$Wallet->type  = 'Cr';
		$Wallet->amount = $amount;
		$Wallet->status = 'Pending';
		$Wallet->transaction_id = $payment->id;
		$Wallet->save();

		// Enter Your Stripe Secret
		\Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
		
		$amount = (int) $amount;
		$amount *= 100;
		$amount = $amount;
		
		$stripe = new \Stripe\StripeClient(env("STRIPE_SECRET"));
		$customer = $stripe->customers->create(
			[
			'name' => $user->name,
			'address' => [
				'line1' => $user->contact_address,
				'postal_code' => $user->contact_zip_code,
				'city' => \App\Helpers\commonHelper::getCityNameById($user->contact_city_id),
				'state' => \App\Helpers\commonHelper::getStateNameById($user->contact_state_id),
				'country' => \App\Helpers\commonHelper::getCountryNameById($user->contact_country_id),
				],
			]
		);

		$payment_intent = \Stripe\PaymentIntent::create([
			'customer'  => $customer['id'], 
			'description' => 'Stripe Test Payment',
			'shipping' => [
				'name' => $user->name,
				'address' => [
					'line1' => $user->contact_address,
					'postal_code' => $user->contact_zip_code,
					'city' => \App\Helpers\commonHelper::getCityNameById($user->contact_city_id),
					'state' => \App\Helpers\commonHelper::getStateNameById($user->contact_state_id),
					'country' => \App\Helpers\commonHelper::getCountryNameById($user->contact_country_id),
				],
			],
			'amount' => $amount,
			'currency' => "USD",
			'payment_method_types' => ['card'],
			"metadata" => ["order_id" => $orderId],
			'capture_method' => 'automatic',
			'confirmation_method' => 'automatic',
		]);

		$intent = $payment_intent->client_secret;
		$payment_intent = $payment_intent->id;
		return ['order_id'=>$orderId,'intent'=>$intent,'payment_intent'=>$payment_intent];
                

	}

	public static function sendSMS($mobile, $template_id = null, $message = null){

		$url = '';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);

	}

	public static function sendZeptoEmail($html){

			$curl = curl_init();

			$postData = [
				'bounce_address'=>"bounces@bounce.yoso.co.in",
				'from'=>[
					'address'=>"noreply@yoso.co.in"
				],
				'to'=>'[{"email_address": {"address": "gopalsaini.img@gmail.com","name": "Vineet"}}]',
				'subject'=>"Test Email",
				'htmlbody'=>$html,
			];
			

			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.zeptomail.in/v1.1/email",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($postData),
			CURLOPT_HTTPHEADER => array(
					"accept: application/json",
					"authorization: Zoho-enczapikey PHtE6r1fQLu4jm8poRkHsfOwFMLyY4sq/r4zLgZDsotDCvVSTk0EqtAokzLhrRgsXfVFEPXOz41rsO/Isr7QITzsYWYYVWqyqK3sx/VYSPOZsbq6x00csFobc03bV4Hncd5t1yHVs9bcNA==",
					"cache-control: no-cache",
					"content-type: application/json",
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				echo $response;
			}

	}

	public static function getUserNameById($id){
		
		$result=\App\Models\User::where('id',$id)->first();
		if($result){
			return ucfirst($result->name.' '.$result->last_name);
		}else{
			return "N/A";
		}
		
	}

	public static function getParticularNameById($id){
		
		$result=\App\Models\Particular::where('id',$id)->first();
		if($result){
			return ucfirst($result->name);
		}else{
			return "N/A";
		}
		
	}

	
    public static function userWithdrawalBalance($userId){
		
		return \App\Models\Wallet::select('wallets.*')
											->join('transactions','transactions.id','=','wallets.transaction_id')
											->where([
													['wallets.status','=','Success'],
													['wallets.type','=','Dr'],
													['wallets.user_id','=',$userId],
													['transactions.particular_id','=','4'],
												])->sum('wallets.amount');	
	
	}
	
	public static function userConfirmBalance($userId){
		
		return \App\Models\Wallet::select('wallets.*')
											->join('transactions','transactions.id','=','wallets.transaction_id')
											->where([
											['wallets.status','=','Success'],
											['wallets.user_id','=',$userId],
											['wallets.type','=','Cr'],
											['transactions.particular_id','=','1'],
												])->sum('wallets.amount');	
	
	}

	public static function userBalance($userId){
		
		$confirmBalance= commonHelper::userConfirmBalance($userId);
		$withdrawalBalance= commonHelper::userWithdrawalBalance($userId);
		
		return $confirmBalance-$withdrawalBalance;
	}


	public static function sendNotificationAndUserHistory($userId,$title,$message,$action,$actionId='0'){
		
		$data=new \App\Models\Notification();
		$data->user_id = $userId;
		$data->title = $title;
		$data->message = $message;
		$data->save();

		$UserHistory=new \App\Models\UserHistory();
		$UserHistory->user_id=$userId;
		$UserHistory->action=$action;
		$UserHistory->action_id=$actionId;
		$UserHistory->save();
	}

	
	public static function getCategoryName($id){
		
		$nameResult=\App\Models\Category::where('id',$id)->first();
		if($nameResult){
			return $nameResult->name;
		}else{
			return 'N/A';
		}
		
		
	}

	
	public static function getFaqCategoryName($id,$lang){
		
		$nameResult=\App\Models\Category::where('id',$id)->first();
		if($nameResult){

			if($lang == 'fr'){

				return $nameResult->fr_name;

			}elseif($lang == 'pt'){

				return $nameResult->pt_name;

			}elseif($lang == 'sp'){

				return $nameResult->sp_name;
				
			}else{

				return $nameResult->name;
			}

			
		}else{
			return 'N/A';
		}
		
		
	}

	public static function ministryPastorTrainerDetail($id){
		
		
		$data=array(
			'Practitioner' => 'Practitioner',
			'Facilitator' => 'Facilitator',
			'Strategist' => 'Strategist',
			'Partner' => 'Donor',
			'Praticien' => 'Practitioner',
			'Facilitateur' => 'Facilitator',
			'Stratège' => 'Strategist',
			'Partenaire' => 'Donor',
			'Praticante' => 'Practitioner',
			'Facilitador' => 'Facilitator',
			'Estrategista/Estratega' => 'Strategist',
			'Parceiro' => 'Donor',
			'Practicante Especializado' => 'Practitioner',
			'Facilitador' => 'Facilitator',
			'Estratega' => 'Strategist',
			'Socio' => 'Donor',
			'N/A' => 'N/A',
			'Mr.'=>'Mr.', // en
			'Sr.'=> 'Mr.', // pt sp
			'Ms.' => 'Ms.', //en
			'Sra.'=> 'Ms.' , // sp
			'Senhorita.' => 'Ms.' , // pt
			'Mrs.'=> 'Mrs.', // en
			'Srta.'=> 'Mrs.', // sp
			'Senhora.'=> 'Mrs.' , // pt
			'Dr.' => 'Dr.',
			'Pasteur' => 'Pastor',
			'Pastor' => 'Pastor', // sp
			'Bishop' => 'Bishop', // en
			'Bispo' => 'Bishop' , //pt
			'Obispo' => 'Bishop', // sp
			'Rev.' => 'Rev.',
			'Prof.' => 'Prof.',
		);
		
		return $data[$id];
	}

	public static function ApiMessageTranslaterLabel($lang,$word){
		
		if($lang == 'sp'){

			$data=array(
				'Please-select-YesNo' => 'Por favor, seleccione SÍ o NO',
				'Please-Group-Users' => 'Por favor, agregue el correo electrónico de los usuarios del grupo.',
				'Wehave-duplicate-email-group-users.' => "Hemos encontrado un correo electrónico duplicado en los usuarios del Grupo.",
				'isalready-exist-please-use-another-email-id' => 'ya existe con nosotros, así que use otra identificación de correo electrónico',
				"Wehave-found-duplicate-mobile-Group-users." => "Hemos encontrado número de teléfono móvil duplicado en usuarios del grupo.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number.' => 'ya existe con nosotros, así que use otro número de teléfono móvil.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers." => "Hemos encontrado un número de whatsApp duplicado en los usuarios de grupo.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number.' => 'Este número de WhatsApp ya existe con nosotros, así que use otro número.',
				'GroupInfo-updated-successfully.' => 'La Información del grupo ha sido actualizada con éxito.',
				'Spouse-not-found' => 'Cónyuge no encontrado',
				'Spouse-already-associated-withother-user' => 'Cónyuge ya asociado con otro usuario',
				'Youhave-already-updated-spouse-detail' => 'Ya ha actualizado los datos del cónyuge',
				'DateOfBirthyear-mustbemore-than-18years' => 'La fecha del año de nacimiento debe ser más de 18 años',
				'Spouse-added-successful'=>'Cónyuge agregado con éxito.',
				'Spouse-update-successful' => 'Actualización de cónyuge exitosa',
				'Stay-room-update-successful' => 'Actualización exitosa de la habitación ',
				'NewPassword-update-successful' => 'Nueva actualización de contraseña exitosa',
				'Profile-updated-successfully' => 'perfil actualizado con éxito',
				'Something-went-wrongPlease-try-again' => 'Algo salió mal. Por favor, vuelva a intentarlo',
				'Contact-Details-updated-successfully.' => 'Detalles del contacto actualizados con éxito.',
				'Youare-not-allowedto-update-profile' => 'No se le permite actualizar perfil',
				'Pastor-detail-not-found' => 'No se encuentra datos del pastor',
				'Profile-details-submit-successfully' => 'Datos del perfil enviados correctamente.(Detalles del perfil Enviar correctamente)',
				'Please-verify-ministry-details' => 'Por favor, verifique los detalles del ministerio',
				'Ministry-Pastor-detail-updated-successfully.' => 'Dato del pastor del ministerio actualizado con éxito.',
				'Your-travelinfo-hasbeenalready-added' => 'Su información de viaje ya ha sido agregada',
				'Travel-Info-Submittedsuccesfully' => 'Información de viaje enviada con éxito',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Su información de viaje se ha enviado con éxito',
				'Please-verify-yourtravel-information' => 'Por favor, verifique su información de viaje',
				'Travel-information-hasbeen-successfully-completed' => 'La información de viaje se ha completado con éxito',	
				'Your-travelInfo-has-been-verified-successfully' => 'Su información de viaje ha sido verificada con éxito',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Su carta preliminar de visa ha sido verificada con éxito. Carta de visa preliminar verificada con éxito',	
				'TravelInfo-doesnot-exist' => 'La información de viaje no existe',	
				'TravelInformation-remarksubmit-successful.' => 'El comentario (Observación) de información de viaje se ingreso con (Enviar) exitoso.',	
				'Youarenot-allowedto-updateTravelInformation' => 'No se le permite actualizar la información de viaje',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Su sesión se ha agregado con éxito',	
				'Session-information-hasbeen-successfully-completed.' => 'La información de la sesión se ha completado con éxito.',	
				'Sessioninfo-doesnot-exists' => 'La información de la sesión no existe',	
				'Session-information-hasbeen-successfullyverified' => 'La información de la sesión se ha verificado con éxito',	
				'Youarenot-allowedto-updatesession-information' => 'No se le permite actualizar la información de la sesión',	
				'Payment-Linksent-succesfully' => 'Enlace de pago enviado con éxito',	
				'Payment-Link' => 'Enlace de pago',	
				'Payment-Successful' => 'Pago exitoso',	
				'Transaction-already-exists' => 'La transacción ya existe',	
				'Transaction-hasbeensent-successfully' => 'La transacción ha sido enviada con éxito',	
				'Requestor-Payment-is-completed' => 'El pago del solicitante ha sido completado',	
				'Offline-payment-successful' => 'Pago fuera de línea exitoso',	
				'Data-not-available' => 'Informacion no disponible',	
				'payment-added-successful' => 'pago agregado con exito ',	
				'No-payment-due' => 'Sin deuda pendiente',	
				'Visa-letter-info-doesnot-exist' => 'La información de la carta de Visa no existe',	
				'Visaletter-file-fetche-succesully' => 'Archivo de carta de visa obtenido con éxito',	
				'Notification-fetched-successfully' => 'Notificación obtenida con éxito',	
				'Emailalready-existsPlease-trywithanother-emailid' => 'El correo electrónico ya existe. Por favor, intente con otro correo electrónico',	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => 'El enlace de verificación de correo electrónico se ha enviado correctamente a su dirección de correo electrónico.',	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => 'Su registro se ha completado con éxito. Por favor, actualice su perfil',	
				'Email-alreadyverifiedPlease-Login' => 'Se ha verificado el correo electrónico, por favor, inicie sesión',	
				'This-account-doesnot-exist' => 'Esta cuenta no existe',	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => 'Tu cuenta ha sido bloqueada. Póngase en contacto con el administrador',	
				'Invalid-Password' => 'Contraseña incorrecta invalida',	
				'Payment-link-hasbeen-expired' => 'El enlace de pago ha expirado',	
				'Payment-Successful' => 'Pago exitoso',	
				'Sponsor-Submitted-Payment' => 'Pago registrado por patrocinador Patrocinador enviado Pago',	
				'Confirmation-link-has-expired' => 'El enlace de confirmación ha sido expirado',	
				'Your-SpouseConfirmation-isRejected!' => 'La confirmación de su cónyuge ha sido rechazada',	
				'Confirmation-Successful' => 'Confirmación exitosa',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Hemos enviado un enlace de restablecimiento de contraseña a su dirección de correo electrónico',	
				'Resetpassword-linkhasbeen-expired' => 'El enlace de contraseña de reinicio ha expirado',	
				'Yourprofile-isunder-review' => 'Su perfil está en revisión',	
				'Your-Travel-Information-pending' => 'Su información de viaje pendiente',	
				'YourSession-Informationpending' => 'La información de su sesión pendiente',	
				'Your-application-alreadyApproved' => 'Su solicitud ya aprobada',	
				'Cash-Payment-addedSuccessful' => 'Pago en efectivo agregado con éxito',	
				'TravelInformation-approved-successful' => 'Información de viaje aprobada con éxito',	
				'Travel-Information-notApproved' => 'Información de viaje no aprobada',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si no puede pagar con su tarjeta de crédito, entonces pague con MoneyGram",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pague su matricula de inscripción utilizando un patrocinio',	
				'Done' =>'realizado',	
				'Youhave-madethe-full-payment' => 'Has realizado el pago completo',	
				'Send-Request' => 'Enviar petición',	
				'Pay-the-full-registration-feewith' => 'Pagar la matricula de inscripción completa con',	
				'Pay-a-little-amount-With' => 'Pagar una pequeña cantidad con',	
				'&-rest-later' => 'y el saldo restante más tarde',	
				'Transaction-Details' => 'Detalles de la transacción',	
				'Order-ID' => 'Numero de solicitud',	
				'You' => 'Usted',	
				'Your-Sponsor' => 'Su patrocinador',	
				'Donation' => 'Donación',	
				'No Transactions Found' => 'No se encontraron transacciones',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon." => "Gracias por enviar su perfil para revisión. Le actualizaremos pronto.",	
				"Account-Rejected" => "Cuenta rechazada",	
				"Sorry-youraccount-didntpassour-verificationsystem" => "Lo sentimos, su cuenta no ha pasado nuestro sistema de verificación",	
				"Vie-Details" => "Ver detalles",	
				'Your-SpouseDetails' => 'Detalles de su cónyuge',	
				'Not-Available' => 'No disponible',	
				'Nothing-Found' => 'No se ha encontrado nada',	
				"You-dont-haveanytravel-informationPlease" => "No tiene información de viaje. Agregue su información de viaje para verla y administrarla aquí",	
				'No-SessionAvailable' => 'No hay sesión disponible',	
				'Something-happenedplease-tryagainlater' => 'Algo sucedio, inténtelo de nuevo más tarde',	
				'Please-submityourprofile-dataOnour-website' => 'Antes de iniciar sesión, introduzca los datos de su perfil en nuestro sitio web.',	
				"Youve-successfullyoffline" => "Ha enviado correctamente el pago fuera de línea para su revisión",	
				"Your-paymentwas-successful" => "El pago se ha realizado correctamente",	
				'Please-checkinputs&try-again' => 'Verifique las entradas e inténtelo de nuevo',
			);

		}elseif($lang == 'fr'){

			$data=array(
				'Please-select-YesNo' => 'Veuillez sélectionner Oui ou Non',
				'Please-Group-Users' => "Veuillez ajouter l'adresse e-mail de l'utilisateur du groupe",
				'Wehave-duplicate-email-group-users.' => "Nous avons trouvé un double de l'e-mail des utilisateurs du groupe.",
				'isalready-exist-please-use-another-email-id' => 'existe déjà avec nous, veuillez utiliser une autre adresse e-mail.',
				"Wehave-found-duplicate-mobile-Group-users." => "Nous avons trouvé un numéro de portable en double chez les utilisateurs du groupe.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number.' => 'existe déjà avec nous, veuillez donc utiliser un autre numéro de portable.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers." => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number.' => 'existe déjà avec nous, veuillez utiliser un autre numéro WhatsApp.',
				'GroupInfo-updated-successfully.' => 'Les informations de groupe ont été mises à jour avec succès.',
				'Spouse-not-found' => 'Conjoint/e introuvable',
				'Spouse-already-associated-withother-user' => 'Conjoint/e déjà associé à un autre utilisateur',
				'Youhave-already-updated-spouse-detail' => 'Vous avez déjà mis à jour les détails du conjoint/e',
				'DateOfBirthyear-mustbemore-than-18years' => 'La date de naissance doit être supérieure à 18 ans.',
				'Spouse-added-successful' => 'Mise à jour du conjoint/e réussi',
				'Spouse-update-successful' => 'Ajout du conjoint/e réussi',
				'Stay-room-update-successful' => "Mise à jour de la chambre d'hôtel a été réussie",
				'NewPassword-update-successful' => 'Mise à jour du nouveau mot de passe réussie',
				'Profile-updated-successfully' => 'Mise à jour du profil réussie',
				'Something-went-wrongPlease-try-again' => "Une erreur s'est produite. Veuillez réessayer",
				'Contact-Details-updated-successfully.' => 'Coordonnées mises à jour avec succès.',
				'Youare-not-allowedto-update-profile' => "Vous n'êtes pas autorisé à mettre à jour le profil",
				'Pastor-detail-not-found' => 'Détail du pasteur introuvable',
				'Profile-details-submit-successfully' => 'Détails du profil ont été soumis avec succès',
				'Please-verify-ministry-details' => 'Veuillez vérifier les détails du ministère',
				'Ministry-Pastor-detail-updated-successfully.' => 'Les détails du ministère du pasteur ont été mis à jour avec succès.',
				'Your-travelinfo-hasbeenalready-added' => 'Vos informations de voyage ont déjà été ajoutées',
				'Travel-Info-Submittedsuccesfully' => 'Informations de voyage soumises avec succès',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Vos informations de voyage ont été envoyées avec succès',
				'Please-verify-yourtravel-information' => 'Veuillez vérifier vos informations de voyage',
				'Travel-information-hasbeen-successfully-completed' => 'Informations de voyage ont été complétées avec succès',	
				'Your-travelInfo-has-been-verified-successfully' => "Vos informations de voyage ont été vérifiées avec succès",	
				'Preliminary-Visa-Letter-successfully-verified' => 'Lettre préliminaire de visa vérifiée avec succès',	
				'TravelInfo-doesnot-exist' => "Informations de voyage n'existent pas",	
				'TravelInformation-remarksubmit-successful.' => "Vous n'êtes pas autorisé à mettre à jour les informations de voyage",	
				'Youarenot-allowedto-updateTravelInformation' => 'Votre session a été ajoutée avec succès',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Informations sur la session ont été complétées avec succès.',	
				'Session-information-hasbeen-successfully-completed.' => 'Session information has been successfully completed.',	
				'Sessioninfo-doesnot-exists' => "Informations de session n'existent pas",	
				'Session-information-hasbeen-successfullyverified' => "Informations de session ont été vérifiées avec succès",	
				'Youarenot-allowedto-updatesession-information' => "Vous n'êtes pas autorisé à mettre à jour les informations de session",	
				'Payment-Linksent-succesfully' => 'Lien de paiement envoyé avec succès',	
				'Payment-Link' => 'Lien de paiement',	
				'Payment-Successful' => 'Paiement réussi',	
				'Transaction-already-exists' => 'Transaction existe déjà',	
				'Transaction-hasbeensent-successfully' => 'Transaction a été envoyée avec succès',	
				'Requestor-Payment-is-completed' => 'Paiement du demandeur a été complété',	
				'Offline-payment-successful' => 'Paiement hors ligne réussi',	
				'Data-not-available' => 'Données non disponibles',	
				'payment-added-successful' => 'paiement ajouté réussi',	
				'No-payment-due' => "Aucun paiement n'est dû",	
				'Visa-letter-info-doesnot-exist' => "Information sur la lettre de visa n'existe pas",	
				'Visaletter-file-fetche-succesully' => 'Fichier de lettre de visa récupéré avec succès',	
				'Notification=fetched-successfully' => 'Notification récupérée avec succès',	
				'Emailalready-existsPlease-trywithanother-emailid' => "E-mail existe déjà. Veuillez essayer un autre identifiant d'e-mail.",	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => "Le lien de vérification de l'e-mail a été envoyé avec succès sur votre adresse e-mail.",	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => "Votre inscription a été complétée avec succès. Veuillez mettre à jour votre profil",	
				'Email-alreadyverifiedPlease-Login' => 'E-mail déjà vérifié. Veuillez vous connecter',	
				'This-account-doesnot-exist' => "Ce compte n'existe pas",	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => "Votre compte a été bloqué. Veuillez contacter l'administrateur",	
				'Invalid-Password' => 'Mot de passe incorrect',	
				'Payment-link-hasbeen-expired' => 'Lien de paiement a été expiré',	
				'Payment-Successful' => 'Paiement réussi',	
				'Sponsor-Submitted-Payment' => 'Paiement soumis par le sponsor',	
				'Confirmation-link-has-expired' => 'Lien de confirmation a été expiré',	
				'Your-SpouseConfirmation-isRejected!' => 'La confirmation de votre conjoint/e a été rejetée!',	
				'Confirmation-Successful' => 'Confirmation réussie',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Nous avons envoyé un lien de réinitialisation de mot de passe sur votre adresse e-mail',	
				'Resetpassword-linkhasbeen-expired' => 'Réinitialiser le lien du mot de passe a été expiré',	
				'Yourprofile-isunder-review' => "Votre profil est en cours d'examen",	
				'Your-Travel-Information-pending' => 'Vos informations de voyage sont en attente',	
				'YourSession-Informationpending' => 'Vos informations de session sont en attente',	
				'Your-application-alreadyApproved' => 'Votre demande a déjà été approuvée',	
				'Cash-Payment-addedSuccessful' => 'Paiement en espèces ajouté avec succès',	
				'TravelInformation-approved-successful' => 'Informations de voyage approuvées réussies',	
				'Travel-Information-notApproved' => 'Informations de voyage non approuvées',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si vous ne parvenez pas à payer avec votre carte de crédit, payez avec MoneyGram",	
				'Payyour-registrationfee-usinga-sponsorship' => "Payez vos frais d'inscription en utilisant un parrainage",	
				'Done' =>'Terminé',	
				'Youhave-madethe-full-payment' => 'Vous avez effectué le paiement complet',	
				'Send-Request' => 'Envoyer une demande',	
				'Pay-the-full-registration-feewith' => "Payer les frais d'inscription complets avec",	
				'Pay-a-little-amount-With' => 'Payer une petite somme avec',	
				'&-rest-later' => '& reposez vous plus tard',	
				'Transaction-Details' => 'Détails de la transaction',	
				'Order-ID' => 'Numéro de commande',	
				'You' => 'Vous',	
				'Your-Sponsor' => 'Votre sponsor',	
				'Donation' => 'Don',	
				'No Transactions Found' => 'Aucune transaction trouvée',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon." => "Merci d'avoir soumis votre profil pour examen. Nous vous tiendrons au courant bientôt.",	
				"Account-Rejected" => "Compte rejeté",	
				"Sorry-youraccount-didntpassour-verificationsystem" => "Désolé, votre compte n'a pas réussi notre système de vérification",	
				"Vie-Details" => "Voir les détails",	
				'Your-SpouseDetails' => 'Les détails de votre conjoint/e',	
				'Not-Available' => 'Pas disponible',	
				'Nothing-Found' => "Rien n'a été trouvé",	
				"You-dont-haveanytravel-informationPlease" => "Vous n'avez aucune information de voyage. Veuillez ajouter vos informations de voyage pour les voir et les gérer ici",	
				'No-SessionAvailable' => 'Aucune session disponible',	
				'Something-happenedplease-tryagainlater' => "Quelque chose s'est produit, veuillez réessayer plus tard",	
				'Please-submityourprofile-dataOnour-website' => "Veuillez d'abord soumettre vos données de profil sur notre site Web avant de vous connecter",	
				"Youhave-successfullyoffline" => "Vous avez réussi à soumettre le paiement hors ligne pour examen",	
				"Your-paymentwas-successful" => "Votre paiement a réussi",	
				'Please-checkinputs&try-again' => 'Veuillez vérifier les entrées et réessayer',
			);

		}elseif($lang == 'pt'){

			$data=array(
				'Please-select-YesNo' => 'Veuillez sélectionner Oui ou Non',
				'Please-Group-Users' => "Veuillez ajouter l'adresse e-mail de l'utilisateur du groupe",
				'Wehave-duplicate-email-group-users.' => "Nous avons trouvé un double de l'e-mail des utilisateurs du groupe.",
				'isalready-exist-please-use-another-email-id' => 'existe déjà avec nous, veuillez utiliser une autre adresse e-mail.',
				"Wehave-found-duplicate-mobile-Group-users." => "Nous avons trouvé un numéro de portable en double chez les utilisateurs du groupe.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'existe déjà avec nous, veuillez donc utiliser un autre numéro de portable.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers." => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number.' => 'existe déjà avec nous, veuillez utiliser un autre numéro WhatsApp.',
				'GroupInfo-updated-successfully.' => 'Les informations de groupe ont été mises à jour avec succès.',
				'Spouse-not-found' => 'Conjoint/e introuvable',
				'Spouse-already-associated-withother-user' => 'Conjoint/e déjà associé à un autre utilisateur',
				'Youhave-already-updated-spouse-detail' => 'Vous avez déjà mis à jour les détails du conjoint/e',
				'DateOfBirthyear-mustbemore-than-18years' => 'La date de naissance doit être supérieure à 18 ans.',
				'Spouse-added-successful' => 'Mise à jour du conjoint/e réussi',
				'Spouse-update-successful' => 'Ajout du conjoint/e réussi',
				'Stay-room-update-successful' => "Mise à jour de la chambre d'hôtel a été réussie",
				'NewPassword-update-successful' => 'Mise à jour du nouveau mot de passe réussie',
				'Profile-updated-successfully' => 'Mise à jour du profil réussie',
				'Something-went-wrongPlease-try-again' => "Une erreur s'est produite. Veuillez réessayer",
				'Contact-Details-updated-successfully.' => 'Coordonnées mises à jour avec succès.',
				'Youare-not-allowedto-update-profile' => "Vous n'êtes pas autorisé à mettre à jour le profil",
				'Pastor-detail-not-found' => 'Détail du pasteur introuvable',
				'Profile-details-submit-successfully' => 'Détails du profil ont été soumis avec succès',
				'Please-verify-ministry-details' => 'Veuillez vérifier les détails du ministère',
				'Ministry-Pastor-detail-updated-successfully.' => 'Les détails du ministère du pasteur ont été mis à jour avec succès.',
				'Your-travelinfo-hasbeenalready-added' => 'Vos informations de voyage ont déjà été ajoutées',
				'Travel-Info-Submittedsuccesfully' => 'Informations de voyage soumises avec succès',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Vos informations de voyage ont été envoyées avec succès',
				'Please-verify-yourtravel-information' => 'Veuillez vérifier vos informations de voyage',
				'Travel-information-hasbeen-successfully-completed' => 'Informations de voyage ont été complétées avec succès',	
				'Your-travelInfo-has-been-verified-successfully' => "Vos informations de voyage ont été vérifiées avec succès",	
				'Preliminary-Visa-Letter-successfully-verified' => 'Lettre préliminaire de visa vérifiée avec succès',	
				'TravelInfo-doesnot-exist' => "Informations de voyage n'existent pas",	
				'TravelInformation-remarksubmit-successful.' => "Vous n'êtes pas autorisé à mettre à jour les informations de voyage",	
				'Youarenot-allowedto-updateTravelInformation' => 'Votre session a été ajoutée avec succès',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Informations sur la session ont été complétées avec succès.',	
				'Session-information-hasbeen-successfully-completed.' => 'Session information has been successfully completed.',	
				'Sessioninfo-doesnot-exists' => "Informations de session n'existent pas",	
				'Session-information-hasbeen-successfullyverified' => "Informations de session ont été vérifiées avec succès",	
				'Youarenot-allowedto-updatesession-information' => "Vous n'êtes pas autorisé à mettre à jour les informations de session",	
				'Payment-Linksent-succesfully' => 'Lien de paiement envoyé avec succès',	
				'Payment-Link' => 'Lien de paiement',	
				'Payment-Successful' => 'Paiement réussi',	
				'Transaction-already-exists' => 'Transaction existe déjà',	
				'Transaction-hasbeensent-successfully' => 'Transaction a été envoyée avec succès',	
				'Requestor-Payment-is-completed' => 'Paiement du demandeur a été complété',	
				'Offline-payment-successful' => 'Paiement hors ligne réussi',	
				'Data-not-available' => 'Données non disponibles',	
				'payment-added-successful' => 'paiement ajouté réussi',	
				'No-payment-due' => "Aucun paiement n'est dû",	
				'Visa-letter-info-doesnot-exist' => "Information sur la lettre de visa n'existe pas",	
				'Visaletter-file-fetche-succesully' => 'Fichier de lettre de visa récupéré avec succès',	
				'Notification=fetched-successfully' => 'Notification récupérée avec succès',	
				'Emailalready-existsPlease-trywithanother-emailid' => "E-mail existe déjà. Veuillez essayer un autre identifiant d'e-mail.",	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => "Le lien de vérification de l'e-mail a été envoyé avec succès sur votre adresse e-mail.",	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => "Votre inscription a été complétée avec succès. Veuillez mettre à jour votre profil",	
				'Email-alreadyverifiedPlease-Login' => 'E-mail déjà vérifié. Veuillez vous connecter',	
				'This-account-doesnot-exist' => "Ce compte n'existe pas",	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => "Votre compte a été bloqué. Veuillez contacter l'administrateur",	
				'Invalid-Password' => 'Mot de passe incorrect',	
				'Payment-link-hasbeen-expired' => 'Lien de paiement a été expiré',	
				'Payment-Successful' => 'Paiement réussi',	
				'Sponsor-Submitted-Payment' => 'Paiement soumis par le sponsor',	
				'Confirmation-link-has-expired' => 'Lien de confirmation a été expiré',	
				'Your-SpouseConfirmation-isRejected!' => 'La confirmation de votre conjoint/e a été rejetée!',	
				'Confirmation-Successful' => 'Confirmation réussie',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Nous avons envoyé un lien de réinitialisation de mot de passe sur votre adresse e-mail',	
				'Resetpassword-linkhasbeen-expired' => 'Réinitialiser le lien du mot de passe a été expiré',	
				'Yourprofile-isunder-review' => "Votre profil est en cours d'examen",	
				'Your-Travel-Information-pending' => 'Vos informations de voyage sont en attente',	
				'YourSession-Informationpending' => 'Vos informations de session sont en attente',	
				'Your-application-alreadyApproved' => 'Votre demande a déjà été approuvée',	
				'Cash-Payment-addedSuccessful' => 'Paiement en espèces ajouté avec succès',	
				'TravelInformation-approved-successful' => 'Informations de voyage approuvées réussies',	
				'Travel-Information-notApproved' => 'Informations de voyage non approuvées',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si vous ne parvenez pas à payer avec votre carte de crédit, payez avec MoneyGram",	
				'Payyour-registrationfee-usinga-sponsorship' => "Payez vos frais d'inscription en utilisant un parrainage",	
				'Done' =>'Terminé',	
				'Youhave-madethe-full-payment' => 'Vous avez effectué le paiement complet',	
				'Send-Request' => 'Envoyer une demande',	
				'Pay-the-full-registration-feewith' => "Payer les frais d'inscription complets avec",	
				'Pay-a-little-amount-With' => 'Payer une petite somme avec',	
				'&-rest-later' => '& reposez vous plus tard',	
				'Transaction-Details' => 'Détails de la transaction',	
				'Order-ID' => 'Numéro de commande',	
				'You' => 'Vous',	
				'Your-Sponsor' => 'Votre sponsor',	
				'Donation' => 'Don',	
				'No Transactions Found' => 'Aucune transaction trouvée',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon." => "Merci d'avoir soumis votre profil pour examen. Nous vous tiendrons au courant bientôt.",	
				"Account-Rejected" => "Compte rejeté",	
				"Sorry-youraccount-didntpassour-verificationsystem" => "Désolé, votre compte n'a pas réussi notre système de vérification",	
				"Vie-Details" => "Voir les détails",	
				'Your-SpouseDetails' => 'Les détails de votre conjoint/e',	
				'Not-Available' => 'Pas disponible',	
				'Nothing-Found' => "Rien n'a été trouvé",	
				"You-dont-haveanytravel-informationPlease" => "Vous n'avez aucune information de voyage. Veuillez ajouter vos informations de voyage pour les voir et les gérer ici",	
				'No-SessionAvailable' => 'Aucune session disponible',	
				'Something-happenedplease-tryagainlater' => "Quelque chose s'est produit, veuillez réessayer plus tard",	
				'Please-submityourprofile-dataOnour-website' => "Veuillez d'abord soumettre vos données de profil sur notre site Web avant de vous connecter",	
				"Youhave-successfullyoffline" => "Vous avez réussi à soumettre le paiement hors ligne pour examen",	
				"Your-paymentwas-successful" => "Votre paiement a réussi",	
				'Please-checkinputs&try-again' => 'Veuillez vérifier les entrées et réessayer',
			);

		}else{

			$data=array(
				'Please-select-YesNo' => 'Please select Yes or No',
				'Please-Group-Users' => 'Please add email of Group Users.',
				'Wehave-duplicate-email-group-users.' => "We've found duplicate email in Group users.",
				'isalready-exist-please-use-another-email-id' => 'is already exist with us so please use another email id',
				"Wehave-found-duplicate-mobile-Group-users." => "We've found duplicate mobile in Group users.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number.' => 'is already exist with us so please use another mobile number.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers." => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number.' => 'is already exist with us so please use another WhatsApp number.',
				'GroupInfo-updated-successfully.' => 'Group Info updated successfully.',
				'Spouse-not-found' => 'Spouse not found',
				'Spouse-already-associated-withother-user' => 'Spouse already associated with other user',
				'Youhave-already-updated-spouse-detail' => 'You have already updated spouse detail',
				'DateOfBirthyear-mustbemore-than-18years' => 'Date of Birth year must be more than 18 years',
				'Spouse-added-successful'=>'Spouse added successful',
				'Spouse-update-successful' => 'Spouse update successful',
				'Stay-room-update-successful' => 'Stay room update successful',
				'NewPassword-update-successful' => 'New Password update successful',
				'Profile-updated-successfully' => 'Profile updated successfully',
				'Something-went-wrongPlease-try-again' => 'Something went wrong. Please try again',
				'Contact-Details-updated-successfully.' => 'Contact Details updated successfully.',
				'Youare-not-allowedto-update-profile' => 'You are not allowed to update profile',
				'Pastor-detail-not-found' => 'Pastor detail not found',
				'Profile-details-submit-successfully' => 'Profile details submit successfully',
				'Please-verify-ministry-details' => 'Please verify ministry details',
				'Ministry-Pastor-detail-updated-successfully.' => 'Ministry Pastor detail updated successfully.',
				'Your-travelinfo-hasbeenalready-added' => 'Your travel info has been already added',
				'Travel-Info-Submittedsuccesfully' => 'Travel Info Submitted succesfully',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Your travel info has been send successfully',
				'Please-verify-yourtravel-information' => 'Please verify your travel information',
				'Travel-information-hasbeen-successfully-completed' => 'Travel information has been successfully completed',	
				'Your-travelInfo-has-been-verified-successfully' => 'Your travel info has been verified successfully',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Preliminary Visa Letter successfully verified',	
				'TravelInfo-doesnot-exist' => 'Travel info does not exist',	
				'TravelInformation-remarksubmit-successful.' => 'Travel information remark submit successful.',	
				'Youarenot-allowedto-updateTravelInformation' => 'You are not allowed to update Travel information',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Your session has been added successfully',	
				'Session-information-hasbeen-successfully-completed.' => 'Session information has been successfully completed.',	
				'Sessioninfo-doesnot-exists' => 'Session info does not exists.',	
				'Session-information-hasbeen-successfullyverified' => 'Session information has been successfully verified',	
				'Youarenot-allowedto-updatesession-information' => 'You are not allowed to update session information',	
				'Payment-Linksent-succesfully' => 'Payment Link sent succesfully',	
				'Payment-Link' => 'Payment Link',	
				'Transaction-already-exists' => 'Transaction already exists',	
				'Transaction-hasbeensent-successfully' => 'Transaction has been sent successfully',	
				'Requestor-Payment-is-completed' => 'Requestor Payment is completed',	
				'Offline-payment-successful' => 'Offline payment successful',	
				'Data-not-available' => 'Data not available',	
				'payment-added-successful' => 'payment added successful',	
				'No-payment-due' => 'No payment due',	
				'Visa-letter-info-doesnot-exist' => 'Visa letter info does not exist',	
				'Visaletter-file-fetche-succesully' => 'Visa letter file fetched succesully',	
				'Notification=fetched-successfully' => 'Notification fetched successfully',	
				'Emailalready-existsPlease-trywithanother-emailid' => 'Email already exists. Please try with another email id',	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => 'Email Verification link has been sent successfully on your email id',	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => 'Your registration has been completed successfully. Please update your profile',	
				'Email-already-verifiedPlease-Login' => 'Email already verified. Please Login',	
				'This-account-doesnot-exist' => 'This account does not exist',	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => 'Your account has been blocked. Please contact Administrator',	
				'Invalid-Password' => 'Invalid Password',	
				'Payment-link-hasbeen-expired' => 'Payment link has been expired',	
				'Payment-Successful' => 'Payment Successful',	
				'Sponsor-Submitted-Payment' => 'Sponsor Submitted Payment',	
				'Confirmation-link-has-expired' => 'Confirmation link has expired',	
				'Your-SpouseConfirmation-isRejected!' => 'Your Spouse Confirmation is Rejected!',	
				'Confirmation-Successful' => 'Confirmation Successful',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'We have sent password reset link on your email address',	
				'Resetpassword-linkhasbeen-expired' => 'Reset password link has been expired',	
				'Yourprofile-isunder-review' => 'Your profile is under review',	
				'Your-Travel-Information-pending' => 'Your Travel Information pending',	
				'YourSession-Informationpending' => 'Your Session Information pending',	
				'Your-application-alreadyApproved' => 'Your application already Approved',	
				'Cash-Payment-addedSuccessful' => 'Cash payment added successful',	
				'TravelInformation-approved-successful' => 'Travel Information approved successful',	
				'Travel-Information-notApproved' => 'Travel Information not approved',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "If you're unable to pay with your credit card then pay using MoneyGram",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pay your registration fee using a sponsorship',	
				'Done' =>'Done',	
				'Youhave-madethe-full-payment' => 'You have made the full payment',	
				'Send-Request' => 'Send Request',	
				'Pay-the-full-registration-feewith' => 'Pay the full registration fee with',	
				'Pay-a-little-amount-With' =>' Pay a little amount with',	
				'&-rest-later' => '& rest later',	
				'Transaction-Details' => 'Transaction Details',	
				'Order-ID' => 'Order ID',	
				'You' => 'You',	
				'Your-Sponsor' => 'Your Sponsor',	
				'Donation' => 'Donation',	
				'No Transactions Found' => 'No Transactions Found',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon." => "Thank you for submitting your profile for review. We'll update you soon.",	
				"Account-Rejected" => "Account Rejected",	
				"Sorry-yourAccount-didntPassOur-verifiCationsystem" => "Sorry, your account didn't pass our verification system",	
				"Vie-Details" => "View Details",	
				'Your-SpouseDetails' => 'Your Spouse Details',	
				'Not-Available' => 'Not Available',	
				'Nothing-Found' => 'Nothing Found',	
				"You-dont-haveanytravel-informationPlease" => "You don't have any travel information. Please add your travel information to see & manage it here",	
				'No-SessionAvailable' => 'No Session Available',	
				'Something-happenedplease-tryagainlater' => 'Something happened, please try again later',	
				'Please-submityourprofile-dataOnour-website' => 'Please submit your profile data on our website first before logging in',	
				"Youve-successfullyoffline" => "You've successfully submitted the offline payment for review",	
				"Your-paymentwas-successful" => "Your payment was successful",	
				'Please-checkinputs&try-again' => 'Please check inputs & try again',	
				
	
				
			);
		}
		
		
		return $data[$word];
	}
}

?>