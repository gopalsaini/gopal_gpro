<?php
namespace App\Helpers;
use Ixudra\Curl\Facades\Curl;
use Session;
use DB;
use Omnipay\Omnipay;

use Stripe;

class commonHelper{

	public static function setLocale(){
        if (\Session::has('lang')) {
            \App::setLocale(\Session::get('lang'));
        }
	}
	
	public static function callAPI($method, $url, $data=array(),$files=array()){
		$token = '';
		if(Session::has('gpro_user')){
			$token = Session::get('gpro_user');
		}elseif(Session::has('gpro_exhibitor')){
			$token = Session::get('gpro_exhibitor');
		}
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
                ->withBearer($token)
				->returnResponseObject()
				->withHeader('Content-Type: application/json')
                ->post();
                
        }elseif($method == 'userTokenget'){
            return $response = Curl::to($url)
            ->withBearer($token)
			->returnResponseObject()
            ->get();
        }elseif($method == 'userTokendelete'){
            return $response = Curl::to($url)
            ->withBearer($token)
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
			$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
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
		// 			$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
		// 			$message->subject($subject);
		// 			$message->to($to);
		// 		});
		// 	}
			
		// }

			$admins = ['ricardo@gprocongress.org','rania@gprocongress.org',env('ADMIN_EMAIL')];

			foreach($admins as $admin){

				$to = $admin;
				\Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
					$message->from(env('MAIL_FROM_ADDRESS'), 'GProCongress II Team');
					$message->subject($subject);
					$message->to($to);
				});
			}

		// $to = env('ADMIN_EMAIL');
		// \Mail::send('email_templates.'.$template, compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
		// 	$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
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
			if($basePrice <= 1075){

				$basePrice = 1075;
			}

			
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
			if($basePrice <= 1075){

				$basePrice = 1075;
			}
			
		}

		return ['basePrice'=>$basePrice,'category'=>$category];

	}

	public static function getBasePriceOfMarriedWSpouse($doyouseek_postoral,$SpouseDoyouseek_postoral,$ministry_pastor_trainer,$SpouseMinistry_pastor_trainer,$base_price) {
			
		$category = [];

		if($ministry_pastor_trainer == 'Yes' && $SpouseMinistry_pastor_trainer == 'Yes'){

			$category = ['Upgrade to Club Floor'=>600,'Upgrade to Suite'=>900,'Day pass'=>1000];
			$basePrice = $base_price*2;
			$trainer = 'Yes';

		}elseif($doyouseek_postoral == 'Yes' && $SpouseDoyouseek_postoral == 'Yes'){

			$category = ['Upgrade to Club Floor'=>600,'Upgrade to Suite'=>900,'Day pass'=>1000];
			$basePrice = $base_price*2;
			$trainer = 'Yes';

		}elseif($ministry_pastor_trainer == 'Yes' && $SpouseDoyouseek_postoral == 'Yes'){

			$category = ['Upgrade to Club Floor'=>600,'Upgrade to Suite'=>900,'Day pass'=>1000];
			$basePrice = $base_price*2;
			$trainer = 'Yes';

		}elseif($doyouseek_postoral == 'Yes' && $SpouseMinistry_pastor_trainer == 'Yes'){

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
			$msg = '<p>Estimado '.$result->name.' '.$result->last_name.',</p><p><br></p>
					<p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p>
					<p>Aquí tiene un resumen actual del estado de su pago: '.$result->amount.'</p><p><br></p>
					<p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p>
					<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p>
					<p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p>
					<p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p>
					<p>Por favor, pague el saldo a más tardar en 31 de Agosto, 2023.</p><p><br></p>
					<p style="background-color:yellow; display: inline;"><i>POR FAVOR, TENGA EN CUENTA: Para poder aprovechar el descuento por “inscripción anticipada”, el pago en su totalidad tiene que recibirse a más tardar el 31 de mayo de 2023.</i></p><p><br></p>
					<p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31 de Agosto, 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p>
					<p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo estará encantado de comunicarse con usted.</p><p><br></p>
					<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
					<p>Para realizar el pago ingrese a <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
					<p>Para mayor información vea el siguiente tutorial https://youtu.be/xSV96xW_Dx0 </p><p>Atentamente,</p><p><br></p>
					<p>El equipo del GProCongress II</p><div><br></div>';
		
		}elseif($result->language == 'fr'){
		
			$subject = "Paiement du solde GProCongrès II: EN ATTENTE";
			$msg = "<p>Cher ".$result->name." ".$result->last_name.",&nbsp;</p><p><br></p>
					<p>Nous vous écrivons pour vous rappeler que vous avez des paiements en attente pour régler le solde dû sur votre compte GProCongrès II.&nbsp;&nbsp;</p>
					<p>Voici un résumé de l’état de votre paiement : '.$result->amount.'</p><p><br></p>
					<p>MONTANT TOTAL À PAYER : ".$totalAcceptedAmount."</p>
					<p>PAIEMENTS EFFECTUÉS ANTÉRIEUREMENT ET ACCEPTÉS : ".$totalAcceptedAmount."</p>
					<p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p>
					<p>SOLDE RESTANT DÛ : ".$totalPendingAmount."</p><p><br></p>
					<p>Veuillez payer le solde au plus tard le&nbsp; 31st August 2023.&nbsp;</p><p><br></p>
					<p style='background-color:yellow; display: inline;'><i>VEUILLEZ NOTER : Afin de bénéficier de la réduction de « l’inscription anticipée », le paiement intégral doit être reçu au plus tard le 31 mai 2023.</i></p><p><br></p>
					<p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous des questions ? Répondez simplement à cet e-mail et notre équipe sera heureuse d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier le nombre et de renforcer les capacités des formateurs de pasteurs.</p><p><br><p><br></p>
					<p>Pour effectuer le paiement, veuillez vous rendre sur <a href='https://www.gprocongress.org/payment' traget='blank'> www.gprocongress.org/payment </a> </p>
					<p>Pour plus d` informations, regardez le tutoriel https://youtu.be/xSV96xW_Dx0 </p> </p>
					<p>Cordialement,</p><div><br></div>";

		}elseif($result->language == 'pt'){
		
			$subject = "Pagamento do Saldo PENDENTE para o II CongressoGPro";
			$msg = '<p>Prezado '.$result->name.'  '.$result->last_name.',&nbsp;</p><p><br></p>
					<p>Estamos escrevendo para lhe lembrar que tem pagamentos pendentes para regularizar o seu saldo em dívida na sua conta para o II CongressoGPro.&nbsp;&nbsp;</p><p><br></p>
					<p>Aqui está o resumo do estado atual do seu pagamento: '.$result->amount.'</p><p><br></p>
					<p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p>
					<p>PAGAMENTO PREVIAMENTE FEITO E ACEITO : '.$totalAcceptedAmount.'</p>
					<p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p>
					<p>SALDO REMANESCENTE EM ABERTO: '.$totalPendingAmount.'</p><p><br></p>
					<p>Por favor pague o saldo até o dia ou antes de 31st August 2023.</p><p><br></p>
					<p style="background-color:yellow; display: inline;"><i>POR FAVOR NOTE: A fim de poder beneficiar do desconto de "adiantamento", o pagamento integral deve ser recebido até 31 de Maio de 2023.</i></p><p><br></p>
					<p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p>
					<p>Alguma dúvida? Simplesmente responda a este e-mail, e nossa equipe estará muito feliz para entrar em contacto com você.&nbsp;</p><p><br></p>
					<p>Ore conosco a medida em que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><p><br></p>
					<p>Para fazer o pagamento, favor ir par <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
					<p>Para mais informações, veja o tutorial https://youtu.be/xSV96xW_Dx0 </p></p>
					<p>Calorosamente,</p>
					<p>A Equipe do II CongressoGPro</p>';
		
		}else{
		
			$subject = 'PENDING: Balance payment for GProCongress II';
			$msg = '<div>Dear '.$name.',&nbsp;</div><div><br></div>
					<div>We are writing to remind you that you have pending payments to settle the balance due on your GProCongress II account.&nbsp;&nbsp;</div><div><br></div>
					<div>Here is a summary of your payment status:</div><div><br></div>
					<div>TOTAL AMOUNT TO BE PAID: '.$result->amount.'</div>
					<div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div>
					<div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div>
					<div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div>
					<div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div>
					<p style="background-color:yellow; display: inline;"><i>PLEASE NOTE: In order to qualify for the “early bird” discount, full payment must be received on or before May 31, 2023</i></p><p><br></p>
					<div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div>
					<div>Do you have questions? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div> Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div>
					<div><p>To make the payment please go to <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
					<p>For more information watch the tutorial https://youtu.be/xSV96xW_Dx0 </p></div>
					<div>Warmly,</div>
					<div>&nbsp;The GProCongress II Team</div>';
			
		}

		\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

		\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
		\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg, 'PENDING: Balance payment for GProCongress II');
					

	}

	public static function paymentGateway($id,$amount = '',$particular = '1',$type = 'stripe') {
		
		
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

		if($particular != '2'){
			\App\Helpers\commonHelper::sendPaymentTriggeredMailSend($user->id,$amount);
		}
		

		if($type == 'stripe'){

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
				'description' => 'Gpro Stripe Online Payment',
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

		if($type == 'paypal'){

			\Session::put('paypal_order_id',$orderId);
			$gateway = Omnipay::create('PayPal_Rest');
			$gateway->setClientId(env('PAYPAL_CLIENT_ID'));
			$gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
			$gateway->setTestMode(true);

			$response = $gateway->purchase(array(
                'amount' => $amount,
                'description' => $orderId,
                'currency' => 'USD',
                'returnUrl' => route('paypal-payment-success'),
                'cancelUrl' => route('paypal-payment-success')
            ))->send();

            if ($response->isRedirect()) {
				
				return ['error'=>false,'url'=>$response->getRedirectUrl()];

            }else{

				return ['error'=>true,'message'=>$response->getMessage()];
            }
		}

		if($type == 'mobile_paypal'){

			
			$gateway = Omnipay::create('PayPal_Rest');
			$gateway->setClientId(env('PAYPAL_CLIENT_ID'));
			$gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
			$gateway->setTestMode(true);

			$response = $gateway->purchase(array(
                'amount' => $amount,
                'description' => $orderId,
                'currency' => 'USD',
                'returnUrl' => route('paypal-payment-success'),
                'cancelUrl' => route('paypal-payment-success')
            ))->send();

            return ['error'=>false,'response'=>$response];

		}
		
                

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
			'Donor' => 'Partner',

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
			'Dr' => 'Dr.',
			'Pasteur' => 'Pastor',
			'Pastor' => 'Pastor', // sp
			'Bishop' => 'Bishop', // en
			'Bispo' => 'Bishop' , //pt
			'Obispo' => 'Bishop', // sp
			'Rev.' => 'Rev.',
			'Prof.' => 'Prof.',
			'' => '',
		);
		
		return $data[$id];
	}

	public static function ApiMessageTranslaterLabel($lang,$word){
		
		if($lang == 'sp'){

			$data=array(
				'Please-select-YesNo' => 'Por favor, seleccione SÍ o NO',
				'Please-Group-Users' => 'Por favor, agregue el correo electrónico de los usuarios del grupo.',
				'Wehave-duplicate-email-group-users' => "Hemos encontrado un correo electrónico duplicado en los usuarios del Grupo.",
				'isalready-exist-please-use-another-email-id' => 'ya existe con nosotros, así que use otra identificación de correo electrónico',
				"Wehave-found-duplicate-mobile-Group-users" => "Hemos encontrado número de teléfono móvil duplicado en usuarios del grupo.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'ya existe con nosotros, así que use otro número de teléfono móvil.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "Hemos encontrado un número de whatsApp duplicado en los usuarios de grupo.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'Este número de WhatsApp ya existe con nosotros, así que use otro número.',
				'GroupInfo-updated-successfully' => 'La Información del grupo ha sido actualizada con éxito.',
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
				'Contact-Details-updated-successfully' => 'Detalles del contacto actualizados con éxito.',
				'Youare-not-allowedto-update-profile' => 'No se le permite actualizar perfil',
				'Pastor-detail-not-found' => 'No se encuentra datos del pastor',
				'Profile-details-submit-successfully' => 'Datos del perfil enviados correctamente.(Detalles del perfil Enviar correctamente)',
				'Please-verify-ministry-details' => 'Por favor, verifique los detalles del ministerio',
				'Ministry-Pastor-detail-updated-successfully' => 'Dato del pastor del ministerio actualizado con éxito.',
				'Your-travelinfo-hasbeenalready-added' => 'Su información de viaje ya ha sido agregada',
				'Travel-Info-Submittedsuccesfully' => 'Información de viaje enviada con éxito',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Su información de viaje se ha enviado con éxito',
				'Please-verify-yourtravel-information' => 'Por favor, verifique su información de viaje',
				'Travel-information-hasbeen-successfully-completed' => 'La información de viaje se ha completado con éxito',	
				'Your-travelInfo-has-been-verified-successfully' => 'Su información de viaje ha sido verificada con éxito',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Su carta preliminar de visa ha sido verificada con éxito. Carta de visa preliminar verificada con éxito',	
				'TravelInfo-doesnot-exist' => 'La información de viaje no existe',	
				'TravelInformation-remarksubmit-successful' => 'El comentario (Observación) de información de viaje se ingreso con (Enviar) exitoso.',	
				'Youarenot-allowedto-updateTravelInformation' => 'No se le permite actualizar la información de viaje',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Su sesión se ha agregado con éxito',	
				'Session-information-hasbeen-successfully-completed' => 'La información de la sesión se ha completado con éxito.',	
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
				'Email-already-verifiedPlease-Login' => 'Se ha verificado el correo electrónico, por favor, inicie sesión',	
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
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si no puede pagar con su tarjeta de crédito, entonces pague con RIA",	
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
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Gracias por enviar su perfil para revisión. Le actualizaremos pronto.",	
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

				
				
				'email_required' => 'La dirección de correo electrónico es obligatoria',	
				'email_email' => 'Introduzca una dirección de correo electrónico válida',	
				'password_required' => 'El campo Contraseña es obligatorio',	
				'password_confirmed_required' => 'Confirmed Password field is required',
				'password_confirmed' => 'La contraseña debe ser la misma que la contraseña de confirmación',	
				'terms_and_condition_required' => 'El campo Términos y Condiciones es obligatorio',	
				'first_name_required' => 'El campo Nombre es obligatorio',	
				'last_name_required' => 'El campo Apellido es obligatorio',	
				'language_required' => 'El campo Idioma es obligatorio',	
				'last_name_string' => 'Se requiere el campo Apellido como secuencia',	
				'token_required' => 'Se requiere código',	
				'name_required' => 'El campo Nombre es obligatorio',	
				'mobile_required' => 'El campo Móvil es obligatorio',	
				'mobile_numeric' => 'El campo Móvil debe ser numérico',	
				'message_required' => 'El campo Mensaje es obligatorio',	
				'phonecode_required' => 'El campo Código de Teléfono es obligatorio',	
				'is_group_required' => 'Por favor, seleccione Sí o No para el grupo',
				'user_whatsup_code_required' => 'El campo código whatsup del usuario es obligatorio',	
				'contact_whatsapp_number_required' => 'el campo número whatsapp de contacto es obligatorio',	
				'user_mobile_code_required' => 'El campo código móvil del usuario es obligatorio',	
				'contact_business_number_required' => 'el campo número de contacto es obligatorio',
				'contact_business_number_unique' => 'The business number has already been taken',	
				'is_spouse_required' => '"¿Viene con su cónyuge al Congreso?" es obligatorio',	
				'is_spouse_registered_required' => 'Cónyuge ya inscrito - por favor confirmar',	
				'id_required' => '"Identificación" es obligatorio',	
				'gender_required' => 'el campo sexo es obligatorio',	
				'email_unique' => 'El correo electrónico ya ha sido tomado',	
				'date_of_birth_required' => 'el campo fecha de nacimiento es obligatorio',	
				'date_of_birth_date' => 'el campo de fecha de nacimiento debe estar en formato de fecha',	
				'citizenship_required' => 'El campo ciudadanía es obligatorio',	
				'salutation_required' => 'el campo saludo es obligatorio',	
				'room_required' => 'el campo habitación es obligatorio',	
				'old_password_required' => 'el campo contraseña antigua es obligatorio',	
				'new_password_required' => 'el campo nueva contraseña es obligatorio',	
				'confirm_password_required' => 'el campo confirmar contraseña es obligatorio',	
				'marital_status_required' => 'el campo estado civil es obligatorio',	
				'contact_address_required' => 'el campo dirección de contacto es obligatorio',	
				'contact_zip_code_required' => 'el campo código postal de contacto es obligatorio',	
				'contact_country_id_required' => 'El campo "País" es obligatorio',	
				'contact_state_id_required' => 'El campo "Estado/Provincia" es obligatorio',	
				'contact_city_id_required' => 'El campo "Ciudad" es obligatorio',	
				'user_mobile_code_required' => 'el campo código móvil del usuario es obligatorio',	
				'contact_business_codenumber_required' => 'el campo código de empresa del contacto es obligatorio',	
				'whatsapp_number_same_mobile_required' => 'el campo del numero de whatsapp es igual al numero de movil es obligatorio',	
				'contact_state_name_required' => 'el campo nombre del estado/provincia de contacto es obligatorio',	
				'contact_city_name' => 'el campo del nombre de la ciudad del contacto es obligatorio',	
				'ministry_address' => 'el campo dirección del ministerio es obligatorio',	
				'ministry_zip_code' => 'el campo código postal del ministerio es obligatorio',	
				'ministry_country_id' => 'el campo id del país del ministerio es obligatorio',	
				'ministry_state_id' => 'el campo id del estado/provincia del ministerio es obligatorio',	
				'ministry_city_id' => 'el campo id de la ciudad del ministerio es obligatorio',	
				'ministry_pastor_trainer' => 'el campo capacitador de pastores del ministerio es obligatorio',	
				'ministry_state_name' => 'el campo nombre del estado/provincia del ministerio es obligatorio',	
				'ministry_city_name' => 'el campo del nombre de la ciudad del ministerio es obligatorio',	
				'non_formal_trainor' => 'Por favor, seleccione una opción en "Capacitación Pastoral No Formal"',	
				'informal_personal' => 'Por favor, seleccione una opción en "Mentoreo Personal Informal"',
				'howmany_pastoral' => 'Por favor, seleccione una opción en "¿Con cuántos líderes está usted involucrado en fortalecer cada año?"',
				'comment_required' => 'el campo comentario es obligatorio',	
				'willing_to_commit' => 'el campo "¿Está dispuesto a comprometerse en formar a un capacitador de pastores al año durante los próximos 7 años?" es obligatorio.',	
				'pastorno' => 'Por favor, seleccione una opción en "¿Cuántos de ellos pueden servir como futuros capacitadores de pastores?"',	
				'arrival_flight_number' => 'el campo número de vuelo de llegada es obligatorio',	
				'arrival_start_location' => 'el campo ubicación de inicio de llegada es obligatorio',	
				'arrival_date_departure' => 'el campo fecha salida de llegada es obligatorio',	
				'arrival_date_arrival' => 'el campo de la fecha de llegada es obligatorio',	
				'departure_flight_number' => 'el campo número de vuelo de salida es obligatorio',	
				'departure_start_location' => 'el campo lugar de salida es obligatorio',	
				'departure_date_departure' => 'el campo fecha de salida del regreso es obligatorio',	
				'departure_date_arrival' => 'el campo fecha de llegada del regreso es obligatorio',	
				'logistics_dropped' => 'Por favor, seleccione Sí o No para "¿Le gustaría a usted y a su cónyuge que el Gpro Congress les llevara de vuelta al aeropuerto?"',	
				'logistics_picked' => 'Por favor, seleccione Sí o No para "¿Le gustaría a usted y a su cónyuge que el GproCongress les recogiera en el aeropuerto?"',
				'spouse_arrival_flight_number' => 'el campo número de vuelo de llegada del cónyuge es obligatorio',	
				'spouse_arrival_start_location' => 'el campo lugar de salida del cónyuge es obligatorio',	
				'spouse_arrival_date_departure' => 'el campo fecha de llegada salida del cónyuge es obligatorio',	
				'spouse_arrival_date_arrival' => 'el campo fecha de llegada del conyuge es obligatorio',	
				'spouse_departure_flight_number' => 'El Número de Vuelo es obligatorio',	
				'spouse_departure_start_location' => 'El lugar de Salida es obligatorio',	
				'spouse_departure_date_departure' => 'Seleccione la Fecha de Salida del Cónyuge',	

				'spouse_departure_date_arrival' => 'Spouse departure date arrival field is required',	

				'status_required_validation' => 'el campo estado es obligatorio',	
				'remark_required_validation' => 'el campo observación es obligatorio',	
				'session_id' => 'El ID de la Sesión es obligatorio',	
				'session_date' => 'La Fecha de la Sesión es obligatoria',	
				'session_required_validation' => 'El Nombre de la Sesión es obligatorio',	
				'amount_required' => 'el campo importe es obligatorio',	
				'amount_numeric' => 'el campo importe debe ser numérico',	
				'mode_required' => 'el campo modo es obligatorio',	
				'reference_number' => 'el campo número de referencia es obligatorio',	
				'country_of_sender' => 'el campo país del remitente es obligatorio',	
				'type' => 'el campo tipo es obligatorio',	
				'user_id' => 'el campo ID de usuario es obligatorio',	
				'amount_lesser_than' => 'Seleccione un importe inferior al pago máximo',	
				'Your_submission_has_been_sent' => 'Su envío se ha realizado correctamente.',	
				'howmany_futurepastor'=> 'Por favor, seleccione una opción en "¿Cuántos de ellos pueden servir como futuros capacitadores de pastores?"',
				'order_required'=> 'order',
				'countries_which_require_authorized'=> 'List of countries which require Authorized or Stamped Visa',
				'requirements_for_authorized_and_stamped_visa'=> 'requirements for Authorized and Stamped Visa',
				'Passport_Number_already_exists'=> 'Passport Number already exists',
				'Invitetion_send_successfully'=> 'Invitetion send successfully',
				'mode_in'=> 'This payment type not support',
				
			);

		}elseif($lang == 'fr'){

			$data=array(
				'Please-select-YesNo' => 'Veuillez sélectionner Oui ou Non',
				'Please-Group-Users' => "Veuillez ajouter l'adresse e-mail de l'utilisateur du groupe",
				'Wehave-duplicate-email-group-users' => "Nous avons trouvé un double de l'e-mail des utilisateurs du groupe.",
				'isalready-exist-please-use-another-email-id' => 'existe déjà avec nous, veuillez utiliser une autre adresse e-mail.',
				"Wehave-found-duplicate-mobile-Group-users" => "Nous avons trouvé un numéro de portable en double chez les utilisateurs du groupe.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'existe déjà avec nous, veuillez donc utiliser un autre numéro de portable.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'existe déjà avec nous, veuillez utiliser un autre numéro WhatsApp.',
				'GroupInfo-updated-successfully' => 'Les informations de groupe ont été mises à jour avec succès.',
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
				'Contact-Details-updated-successfully' => 'Coordonnées mises à jour avec succès.',
				'Youare-not-allowedto-update-profile' => "Vous n'êtes pas autorisé à mettre à jour le profil",
				'Pastor-detail-not-found' => 'Détail du pasteur introuvable',
				'Profile-details-submit-successfully' => 'Détails du profil ont été soumis avec succès',
				'Please-verify-ministry-details' => 'Veuillez vérifier les détails du ministère',
				'Ministry-Pastor-detail-updated-successfully' => 'Les détails du ministère du pasteur ont été mis à jour avec succès.',
				'Your-travelinfo-hasbeenalready-added' => 'Vos informations de voyage ont déjà été ajoutées',
				'Travel-Info-Submittedsuccesfully' => 'Informations de voyage soumises avec succès',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Vos informations de voyage ont été envoyées avec succès',
				'Please-verify-yourtravel-information' => 'Veuillez vérifier vos informations de voyage',
				'Travel-information-hasbeen-successfully-completed' => 'Informations de voyage ont été complétées avec succès',	
				'Your-travelInfo-has-been-verified-successfully' => "Vos informations de voyage ont été vérifiées avec succès",	
				'Preliminary-Visa-Letter-successfully-verified' => 'Lettre préliminaire de visa vérifiée avec succès',	
				'TravelInfo-doesnot-exist' => "Informations de voyage n'existent pas",	
				'TravelInformation-remarksubmit-successful' => "Vous n'êtes pas autorisé à mettre à jour les informations de voyage",	
				'Youarenot-allowedto-updateTravelInformation' => 'Votre session a été ajoutée avec succès',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Informations sur la session ont été complétées avec succès.',	
				'Session-information-hasbeen-successfully-completed' => 'Session information has been successfully completed.',	
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
				'Notification-fetched-successfully' => 'Notification récupérée avec succès',	
				'Emailalready-existsPlease-trywithanother-emailid' => "E-mail existe déjà. Veuillez essayer un autre identifiant d'e-mail.",	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => "Le lien de vérification de l'e-mail a été envoyé avec succès sur votre adresse e-mail.",	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => "Votre inscription a été complétée avec succès. Veuillez mettre à jour votre profil",	
				'Email-already-verifiedPlease-Login' => 'E-mail déjà vérifié. Veuillez vous connecter',	
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
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si vous ne parvenez pas à payer avec votre carte de crédit, payez avec RIA",	
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
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Merci d'avoir soumis votre profil pour examen. Nous vous tiendrons au courant bientôt.",	
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

				
				
				'email_required' => "L'e-mail est exigé",	
				'email_email' => 'Veuillez entrer une adresse e-mail valide',	
				'password_required' => 'Le champ du mot de passe est exigé',	
				'password_confirmed_required' => 'confirmation Le champ du mot de passe est exigé',
				'password_confirmed' => 'Mot de passe doit être le même que le mot de passe de confirmation',	
				'terms_and_condition_required' => 'Le champ "Termes et Conditions " est exigé',	
				'first_name_required' => 'Le champ "Prénom" est exigé',	
				'last_name_required' => 'Le champ "Nom de famille" est exigé',	
				'language_required' => 'Le champ "Langue" est exigé',	
				'last_name_string' => 'Le champ "Nom de famille" est exigé en tant que chaîne de caractères',	
				'token_required' => 'Un jeton est exigé',	
				'name_required' => 'Le champ "Nom" est exigé',	
				'mobile_required' => 'Le champ "Téléphone portable" est exigé',	
				'mobile_numeric' => 'Le champ "Téléphone portable" doit être un numéro',	
				'message_required' => 'Le champ de"Message" est exigé',	
				'phonecode_required' => 'Le champ "Code téléphonique" est exigé',	
				'is_group_required' => 'Veuillez sélectionner Oui ou Non pour le groupe',	
				'user_whatsup_code_required' => "le champ Code WhatsApp de l'utilisateur est exigé",	
				'contact_whatsapp_number_required' => 'le champ "Numéro WhatsApp du contact" est exigé',	
				'user_mobile_code_required' => "le champ Code du téléphone portable de l'utilisateur est exigé",	
				'contact_business_number_required' => "le champ Numéro de téléphone du travail est exigé",

				'contact_business_number_unique' => 'The business number has already been taken',
					
				'is_spouse_required' => '"Venez-vous avec votre conjoint/e au Congrès ?" est exigé',	
				'is_spouse_registered_required' => 'Conjoint/e déjà inscrit - veuillez confirmer',	
				'id_required' => '"id" est exigé',	
				'gender_required' => 'Le champ "sexe" est exigé',	
				'email_unique' => "L'e-mail a déjà été pris.",	
				'date_of_birth_required' => 'le champ "date de naissance" est exigé',	
				'date_of_birth_date' => 'Le champ "date de naissance" doit être un format de date.',	
				'citizenship_required' => 'le champ "citoyenneté" est exigé',	
				'salutation_required' => 'le champ "salutation" est exigé',	
				'room_required' => 'le champ "chambre" est exigé',	
				'old_password_required' => "le champ de l'ancien mot de passe est exigé",	
				'new_password_required' => 'le champ du "nouveau mot de passe" est exigé',	
				'confirm_password_required' => 'le champ "confirmer le mot de passe" est exigé',	
				'marital_status_required' => 'le champ "état civil" est exigé',	
				'contact_address_required' => 'le champ "adresse de contact" est exigé',	
				'contact_zip_code_required' => 'le champ "code postal" du contact est exigé',	
				'contact_country_id_required' => 'le champ "ID du pays" exigé',	
				'contact_state_id_required' => "le champ ID de l' État/Province est exigé",	
				'contact_city_id_required' => 'le champ "Ville"est exigé',	
				'user_mobile_code_required' => "le champ du code du téléphone portable de l'utilisateur est exigé",	
				'contact_business_codenumber_required' => 'Le champ du "code du numéro de téléphone du travail" est requis',	
				'whatsapp_number_same_mobile_required' => 'le champ du "numéro WhatsApp identique au numéro de portable" est exigé',	
				'contact_state_name_required' => "le champ du nom de l'état/province du contact est exigé",	
				'contact_city_name' => 'le champ du "Nom de la ville" du contact est exigé',	
				'ministry_address' => 'le champ "Adresse du ministère" est exigé',	
				'ministry_zip_code' => 'le champ du "Code postal du ministère" est exigé',	
				'ministry_country_id' => 'le champ "ID du pays du ministère" est exigé',	
				'ministry_state_id' => "Le champ de 'ID de l'état/province du ministère' est exigé",	
				'ministry_city_id' => 'Le champ de "ID de la ville du ministère" est exigé',	
				'ministry_pastor_trainer' => 'le champ de "formateur de pasteur du ministère" est exigé',	
				'ministry_state_name' => "le champ du 'nom de l'état/province du ministère' est exigé",	
				'ministry_city_name' => 'le champ du "nom de la ville du ministère" est exigéd',	
				'non_formal_trainor' => 'Veuillez choisir une option dans "Formation pastorale non formelle"',	
				'informal_personal' => 'Veuillez choisir une option dans "Mentorat personnel informel"',	
				'howmany_pastoral' => 'Veuillez sélectionner une option dans "Combien de responsables pastoraux participez-vous à renforcer chaque année ?"',	
				'comment_required' => 'le champ "commentaire" est exigé',	
				'willing_to_commit' => '"Êtes-vous prêt à vous engager à former un formateur de pasteurs par an pendant les 7 prochaines années ?" est exigé.',	
				'pastorno' => 'le champ de "combien de pasteurs au futur" est exigé',	
				'arrival_flight_number' => "le champ du numéro du vol d'arrivée est exigé",	
				'arrival_start_location' => "le champ de l'arrivée du lieu de départ est exigé",	
				'arrival_date_departure' => 'le champ de la date d arrivée départ est exigé',	
				'arrival_date_arrival' => "le champ de la date d'arrivée est exigé",	
				'departure_flight_number' => 'le champ du "numéro de vol de départ" est exigé',	
				'departure_start_location' => 'Le champ du "lieu de départ" est exigé',	
				'departure_date_departure' => 'le champ de "la date de départ" est exigé',	
				'departure_date_arrival' => 'le champ de "la date de départ arrivée" est exigé',
				'logistics_dropped' => 'Veuillez sélectionner Oui ou Non pour "Souhaitez-vous que votre conjoint(e) et vous-même soyez déposés par Gpro Congress à l aéroport ?"',	
				'logistics_picked' => 'Veuillez sélectionner Oui ou Non pour Souhaitez-vous que votre conjoint(e) et vous soyez pris(e) en charge par Gpro Congress à l aéroport ?',	
				'spouse_arrival_flight_number' => 'le champ du numéro de vol d arrivée du conjoint/e est exigé',	
				'spouse_arrival_start_location' => "le champ du'lieu de départ de l'arrivée du conjoint/e' est exigé",	
				'spouse_arrival_date_departure' => "le champ de'la date d'arrivée du conjoint/e' est exigé",	
				'spouse_arrival_date_arrival' => "le champ de'la date d arrivée du conjoint/e est exigé",	
				'spouse_departure_flight_number' => 'Le numéro de vol est exigé',	
				'spouse_departure_start_location' => 'Le lieu de départ est exigé',	
				'spouse_departure_date_departure' => 'Veuillez sélectionner la date de départ du conjoint/e',

				'spouse_departure_date_arrival' => 'Spouse departure date arrival field is required',	

				'status_required_validation' => 'le champ "statut" est exigé',	
				'remark_required_validation' => 'le champ "remarque" est exigé',	
				'session_id' => '"ID de la session" est exigé',	
				'session_date' => '"date de la session" est exigé',	
				'session_required_validation' => '"le nom de la session" est exigé',	
				'amount_required' => 'le champ "montant" est exigé',	
				'amount_numeric' => 'Le montant doit être un chiffre',	
				'mode_required' => 'le champ "mode" est exigé',	
				'reference_number' => 'le champ "numéro de référence" est exigé',	
				'country_of_sender' => "le champ 'pays de l'expéditeur' est exigé",	
				'type' => 'le champ "type" est exigé',	
				'user_id' => "le champ 'ID de l'utilisateur' est exigé",	
				'amount_lesser_than' => 'Veuillez sélectionner un montant inférieur au paiement maximum',	
				'Your_submission_has_been_sent' => 'Votre demande a été envoyée avec succès.',
				'howmany_futurepastor'=> 'le champ de "combien de pasteurs au futur" est exigé',	
				'order_required'=> 'order',
				'countries_which_require_authorized'=> 'List of countries which require Authorized or Stamped Visa',
				'requirements_for_authorized_and_stamped_visa'=> 'requirements for Authorized and Stamped Visa',
				'Passport_Number_already_exists'=> 'Passport Number already exists',
				'Invitetion_send_successfully'=> 'Invitetion send successfully',
				'mode_in'=> 'This payment type not support',
				
			);

		}elseif($lang == 'pt'){

			$data=array(
				
				'Please-select-YesNo' => 'Por favor selecione Sim ou Não',
				'Please-Group-Users' => 'Por favor, adicione o email do Grupo de Usuários.',
				'Wehave-duplicate-email-group-users' => "Encontramos e-mail duplicado no Grupo de usuários",
				'isalready-exist-please-use-another-email-id' => 'já tem uma conta conosco, por favor use outro e-mail',
				"Wehave-found-duplicate-mobile-Group-users" => "Encontramos numeros de telefone duplicados no Grupo de usuários",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'Já tem uma conta conosco, por favor usar outro número de telefone.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "Encontramos o número de WhatsApp duplicado no Grupo de usuários",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'Já tem uma conta conosco, por favor use outro número de WhatsApp',
				'GroupInfo-updated-successfully' => 'Informações do grupo atualizadas com sucesso',
				'Spouse-not-found' => 'Cônjuge não encontrado',
				'Spouse-already-associated-withother-user' => 'Cônjuge já associado a outro usuário',
				'Youhave-already-updated-spouse-detail' => 'Você já atualizou o detalhe do cônjuge',
				'DateOfBirthyear-mustbemore-than-18years' => 'A data do ano de nascimento deve ser superior a 18 anos',
				'Spouse-added-successful'=>'Adicionou o cônjuge com sucesso',
				'Spouse-update-successful' => 'Atualização do cônjuge bem -sucedido',
				'Stay-room-update-successful' => 'Atualização da Sala de espera feita com sucesso',
				'NewPassword-update-successful' => 'Nova atualização da senha bem -sucedida',
				'Profile-updated-successfully' => 'Perfil atualizado com sucesso',
				'Something-went-wrongPlease-try-again' => 'Algo deu errado. Por favor tente novamente',
				'Contact-Details-updated-successfully' => 'Detalhes de contato atualizados com sucesso.',
				'Youare-not-allowedto-update-profile' => 'Você não tem permissão para atualizar o perfil',
				'Pastor-detail-not-found' => 'Informacao do Pastor não encontrada',
				'Profile-details-submit-successfully' => 'Detalhes do perfil enviados com sucesso',
				'Please-verify-ministry-details' => 'Por favor, verifique os detalhes do ministério',
				'Ministry-Pastor-detail-updated-successfully' => 'Informacao detalhada do ministerio do pastor atualizado com sucesso',
				'Your-travelinfo-hasbeenalready-added' => 'Suas informações de viagem já foram adicionadas',
				'Travel-Info-Submittedsuccesfully' => 'Informações de viagem enviadas com sucesso',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'As suas informações de viagem foram enviadas com sucesso',
				'Please-verify-yourtravel-information' => 'Por favor, verifique suas informações de viagem',
				'Travel-information-hasbeen-successfully-completed' => 'A informaçao sobre a viagem foi completada com sucesso',	
				'Your-travelInfo-has-been-verified-successfully' => 'Suas informações de viagem foram verificadas com sucesso',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Carta de visto preliminar verificada com sucesso',	
				'TravelInfo-doesnot-exist' => 'Informações de viagem não existem',	
				'TravelInformation-remarksubmit-successful' => 'Comentario de informações de viagem enviado com sucesso',	
				'Youarenot-allowedto-updateTravelInformation' => 'Nao tem permissao para atualizar as informações sobre viagem',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Sua sessão foi adicionada com sucesso',	
				'Session-information-hasbeen-successfully-completed' => 'A informaçao da sessão foi realizada com sucesso',	
				'Sessioninfo-doesnot-exists' => 'Informações da sessão não existem',	
				'Session-information-hasbeen-successfullyverified' => 'As informações da sessão foram verificadas com sucesso',	
				'Youarenot-allowedto-updatesession-information' => 'Você não tem permissão para atualizar as informações da sessão',	
				'Payment-Linksent-succesfully' => 'Link de pagamento enviado com sucesso',	
				'Payment-Link' => 'Link de pagamento',	
				'Transaction-already-exists' => 'Transação já existe',	
				'Transaction-hasbeensent-successfully' => 'A transação foi enviada com sucesso',	
				'Requestor-Payment-is-completed' => 'O pagamento do solicitante esta concluída',	
				'Offline-payment-successful' => 'Pagamento offline bem-sucedido',	
				'Data-not-available' => 'Dados não disponíveis',	
				'payment-added-successful' => 'pagamento adicionado bem -sucedido',	
				'No-payment-due' => 'Nenhum pagamento pendente',	
				'Visa-letter-info-doesnot-exist' => 'Informações da carta de visto não existe',	
				'Visaletter-file-fetche-succesully' => 'Arquivo de cartas de visto feito com sucesso',	
				'Notification-fetched-successfully' => 'Notificação obtida com sucesso',	
				'Emailalready-existsPlease-trywithanother-emailid' => 'E-mail já existe. Por favor, tente com outro e-mail',	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => 'O link de verificação de email foi enviado com sucesso para o seu e -mail',	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => 'Seu registro foi concluído com sucesso, por favor atualize seu perfil',	
				'Email-already-verifiedPlease-Login' => 'E -mail já verificado, por favor, inicie a sessao',	
				'This-account-doesnot-exist' => 'Essa conta não existe',	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => 'Sua conta foi bloqueada. Por favor contacte o Administrador',	
				'Invalid-Password' => 'Senha Inválida',	
				'Payment-link-hasbeen-expired' => 'O link de pagamento expirou',	
				'Payment-Successful' => 'Pagamento efetuado com sucesso',	
				'Sponsor-Submitted-Payment' => 'Patrocinador realizou o pagamento',	
				'Confirmation-link-has-expired' => 'O link de confirmação expirou',	
				'Your-SpouseConfirmation-isRejected!' => 'A confirmação do seu cônjuge foi rejeitada!',	
				'Confirmation-Successful' => 'Confirmação efetuada com sucesso',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Enviamos o link de redefinição de senha para o seu e-mail',	
				'Resetpassword-linkhasbeen-expired' => 'O link da redifinicao de senha ja expirou',	
				'Yourprofile-isunder-review' => 'Seu perfil está sendo revisto',	
				'Your-Travel-Information-pending' => 'Suas informações de viagem estao pendentes',	
				'YourSession-Informationpending' => 'Informacoes de sua sessao esta pendente',	
				'Your-application-alreadyApproved' => 'Sua inscrição já foi aprovada',	
				'Cash-Payment-addedSuccessful' => 'Pagamento em especie adicionado com sucesso',	
				'TravelInformation-approved-successful' => 'Informações de viagem aprovada com sucesso',	
				'Travel-Information-notApproved' => 'Informações de viagem não aprovadas',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Se você não puder pagar com seu cartão de crédito, pague usando RIA",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pague sua taxa de inscrição usando um patrocínio',	
				'Done' =>'Feito',	
				'Youhave-madethe-full-payment' => 'Você fez o pagamento integral',	
				'Send-Request' => 'Enviar pedido',	
				'Pay-the-full-registration-feewith' => 'Pagar a taxa de inscricao completa ',	
				'Pay-a-little-amount-With' => 'Pague uma pequena quantia com',	
				'&-rest-later' => 'e o restante pague depois',	
				'Transaction-Details' => 'Detalhes da transação',	
				'Order-ID' => 'ID do pedido',	
				'You' => 'Você',	
				'Your-Sponsor' => 'Seu patrocinador',	
				'Donation' => 'Doação',	
				'No Transactions Found' => 'Nenhuma transação encontrada',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Agradecemos por enviar seu perfil para revisão. Vamos atualizá -lo em breve",	
				"Account-Rejected" => "Conta rejeitada",	
				"Sorry-yourAccount-didntPassOur-verifiCationsystem" => "Desculpe, sua conta não passou no sistema de verificação",	
				"Vie-Details" => "Ver detalhes",	
				'Your-SpouseDetails' => 'Detalhes do seu cônjuge',	
				'Not-Available' => 'Não disponível',	
				'Nothing-Found' => 'Nada encontrado',	
				"You-dont-haveanytravel-informationPlease" => "Você não tem qualquer informação de viagem. Por favor adicione suas informações de viagem para a ver e gerencir por aqui",	
				'No-SessionAvailable' => 'Nenhuma sessão disponível',	
				'Something-happenedplease-tryagainlater' => 'Algo aconteceu, por favor tente novamente mais tarde',	
				'Please-submityourprofile-dataOnour-website' => 'Por favor submeta os seus dados de perfil em nosso site antes de iniciar a sessao',	
				"Youve-successfullyoffline" => "Você enviou com sucesso o pagamento offline para revisão",	
				"Your-paymentwas-successful" => "Seu pagamento foi efetuado com sucesso",	
				'Please-checkinputs&try-again' => 'Por favor, verifique as entradas e tente novamente',


				'email_required' => 'O e-mail é obrigatorio',	
				'email_email' => 'Por favor introduza um e-mail válido',	
				'password_required' => 'A Senha é obrigatória',	
				'password_confirmed_required' => 'A senha deve ser a mesma que a senha de confirmacao',
				'password_confirmed' => 'A senha deve ser a mesma que a senha de confirmacao',	
				'terms_and_condition_required' => 'O campo: Termos e Condição é obrigatório',	
				'first_name_required' => 'O campo: Nome é obrigatório',	
				'last_name_required' => 'O campo: Sobrenome é obrigatório',	
				'language_required' => 'O campo: Idiomas é obrigatório',	
				'last_name_string' => 'O campo: Sobrenome é obrigatório',	
				'token_required' => 'O Token é obrigatorio',	
				'name_required' => 'O campo: Nome é obrigatório',	
				'mobile_required' => 'Campo: Telefone móvel é obrigatorio',	
				'mobile_numeric' => 'O campo do telefone móvel deve ser numérico',	
				'message_required' => 'O campo: Mensagem é obrigatório',	
				'phonecode_required' => 'O campo: código telefónico é obrigatório',	
				'is_group_required' => 'O campo: grupo é obrigatorio',	
				'user_whatsup_code_required' => 'campo: código do usuario whatsapp é obrigatorio',	
				'contact_whatsapp_number_required' => 'O campo: contato do numero de whatsapp é necessário',	
				'user_mobile_code_required' => 'o campo: código de telemovel do usuario é obrigatório',	
				'contact_business_number_required' => 'campo: número da empresa do usuario é obrigatório',

				'contact_business_number_unique' => 'The business number has already been taken',

				'is_spouse_required' => 'O campo: cônjuge é obrigatório',	
				'is_spouse_registered_required' => 'O campo: registo de cônjuge é obrigatório',	
				'id_required' => 'campo: Identificacao é necessário',	
				'gender_required' => 'o campo: género é necessário',	
				'email_unique' => 'O e-mail já foi recebido.',	
				'date_of_birth_required' => 'O campo: data de nascimento é obrigatório',	
				'date_of_birth_date' => 'o campo: data de nascimento deve ser um formulário de data',	
				'citizenship_required' => 'o campo: cidadania é obrigatório',	
				'salutation_required' => 'campo: saudação é necessário',	
				'room_required' => 'O campo: quarto é necessário',	
				'old_password_required' => 'campo: senha antiga é obrigatória',	
				'new_password_required' => 'é necessário um novo campo de senha',	
				'confirm_password_required' => 'O campo: confirmar a senha é obrigatório',	
				'marital_status_required' => 'o campo: estado civil é obrigatório',	
				'contact_address_required' => 'o campo: endereço de contacto é obrigatório',	
				'contact_zip_code_required' => 'campo: código postal de contato é obrigatório',	
				'contact_country_id_required' => 'campo: identificação do país de contacto é obrigatório',	
				'contact_state_id_required' => 'campo: identificação do estado de contacto é necessário',	
				'contact_city_id_required' => 'campo: identificação da cidade de é obrigatório',	
				'user_mobile_code_required' => 'o campo: código de telemóvel do usuario é obrigatório',	
				'contact_business_codenumber_required' => 'campo: código comercial é obrigatório',	
				'whatsapp_number_same_mobile_required' => 'o que é necessário para o número de telemóvel',	
				'contact_state_name_required' => 'o campo: nome do estado é obrigatório',	
				'contact_city_name' => 'o campo: nome da cidade é obrigatório',	
				'ministry_address' => 'o campo: endereço do ministério é obrigatório',	
				'ministry_zip_code' => 'o campo: código postal do ministério é obrigatório',	
				'ministry_country_id' => 'o campo: identificação do país do ministério é obrigatório',	
				'ministry_state_id' => 'campo: identificacao do estado do ministério é obrigatório',	
				'ministry_city_id' => 'campo: identificacao da cidade do ministério é obrigatório',	
				'ministry_pastor_trainer' => 'o campo: formadores de pastores do ministério é obrigatório',	
				'ministry_state_name' => 'O campo: nome do estado do ministério é obrigatório',	
				'ministry_city_name' => 'O campo: nome da cidade do ministério é obrigatório',	
				'non_formal_trainor' => 'O Campo: Treinador não formal é obrigatório',	
				'informal_personal' => 'O Campo: Treinador formal é obrigatório',	
				'howmany_pastoral' => 'O Campo: quantos treinadores pastorais é obrigatório',	
				'comment_required' => 'O campo: comentários é obrigatório',	
				'willing_to_commit' => 'é necessário estar disposto a comprometer-se no terreno',	
				'pastorno' => 'O Campo: quantos futuros pastores é obrigatorio',
				'arrival_flight_number' => 'o campo: número do voo de chegada é obrigatório',	
				'arrival_start_location' => 'o campo: localização de início de chegada é obrigatório',	
				'arrival_date_departure' => 'O Campo: data de chegada é obrigatório',	
				'arrival_date_arrival' => 'campo: data de chegada é obrigatório',	
				'departure_flight_number' => 'o campo: número do voo de partida é obrigatório',	
				'departure_start_location' => 'O campo: local de início de partida é obrigatório',	
				'departure_date_departure' => 'O campo: data de partida é obrigatório',	
				'departure_date_arrival' => 'O campo: data de chegada é obrigatório',	
				'logistics_dropped' => 'é necessário escolher o campo logístico',	
				'logistics_picked' => 'é necessária uma logística de campo abandonado',	
				'spouse_arrival_flight_number' => 'o campo: número do voo de chegada do cônjuge é obrigatório',	
				'spouse_arrival_start_location' => 'o campo: localização de início de chegada do cônjuge é obrigatório',	
				'spouse_arrival_date_departure' => 'O Campo: data de partida do cônjuge é obrigatório',	
				'spouse_arrival_date_arrival' => 'o campo:: data de chegada do cônjuge é obrigatório',	
				'spouse_departure_flight_number' => 'o campo: número de voo de partida do cônjuge é obrigatório',	
				'spouse_departure_start_location' => 'o campo: local de partida do cônjuge é obrigatório',	
				'spouse_departure_date_departure' => 'campo: data de partida do cônjuge é obrigatório',	
				'spouse_departure_date_arrival' => 'o campo: data de partida do cônjuge é obrigatório',	
				'status_required_validation' => 'o campo: estado é obrigatório',	
				'remark_required_validation' => 'campo: observação é obrigatório',	
				'session_id' => 'o campo: identificação da sessão é obrigatório',	
				'session_date' => 'o campo: data da sessão é obrigatório',	
				'session_required_validation' => 'campo: sessão é necessário',	
				'amount_required' => 'campo: quantia é necessário',	
				'amount_numeric' => 'quantidade deve ser em número',	
				'mode_required' => 'o campo: modo é necessário',	
				'reference_number' => 'o campo: número de referência é obrigatório',	
				'country_of_sender' => 'O campo: país do remetente é obrigatório',	
				'type' => 'O campo: escrever é obrigatório',	
				'user_id' => 'O campo: identificação do usuario é obrigatório',	
				'amount_lesser_than' => 'Por favor, selecione um montante inferior ao valor máximot',	
				'Your_submission_has_been_sent' => 'A sua submissão foi realizada com sucesso',	
				'howmany_futurepastor'=> 'O Campo: quantos futuros pastores é obrigatorio',
				'order_required'=> 'order',
				'countries_which_require_authorized'=> 'List of countries which require Authorized or Stamped Visa',
				'requirements_for_authorized_and_stamped_visa'=> 'requirements for Authorized and Stamped Visa',
				'Passport_Number_already_exists'=> 'Passport Number already exists',
				'Invitetion_send_successfully'=> 'Invitetion send successfully',
				'mode_in'=> 'This payment type not support',

				
			);

		}else{

			$data=array(
				'Please-select-YesNo' => 'Please select Yes or No',
				'Please-Group-Users' => 'Please add email of Group Users.',
				'Wehave-duplicate-email-group-users' => "We've found duplicate email in Group users.",
				'isalready-exist-please-use-another-email-id' => 'is already exist with us so please use another email id',
				"Wehave-found-duplicate-mobile-Group-users" => "We've found duplicate mobile in Group users.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'is already exist with us so please use another mobile number.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'is already exist with us so please use another WhatsApp number.',
				'GroupInfo-updated-successfully' => 'Group Info updated successfully.',
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
				'Contact-Details-updated-successfully' => 'Contact Details updated successfully.',
				'Youare-not-allowedto-update-profile' => 'You are not allowed to update profile',
				'Pastor-detail-not-found' => 'Pastor detail not found',
				'Profile-details-submit-successfully' => 'Profile details submit successfully',
				'Please-verify-ministry-details' => 'Please verify ministry details',
				'Ministry-Pastor-detail-updated-successfully' => 'Ministry Pastor detail updated successfully.',
				'Your-travelinfo-hasbeenalready-added' => 'Your travel info has been already added',
				'Travel-Info-Submittedsuccesfully' => 'Travel Info Submitted succesfully',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Your travel info has been send successfully',
				'Please-verify-yourtravel-information' => 'Please verify your travel information',
				'Travel-information-hasbeen-successfully-completed' => 'Travel information has been successfully completed',	
				'Your-travelInfo-has-been-verified-successfully' => 'Your travel info has been verified successfully',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Preliminary Visa Letter successfully verified',	
				'TravelInfo-doesnot-exist' => 'Travel info does not exist',	
				'TravelInformation-remarksubmit-successful' => 'Travel information remark submit successful.',	
				'Youarenot-allowedto-updateTravelInformation' => 'You are not allowed to update Travel information',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Your session has been added successfully',	
				'Session-information-hasbeen-successfully-completed' => 'Session information has been successfully completed.',	
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
				'Notification-fetched-successfully' => 'Notification fetched successfully',	
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
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "If you're unable to pay with your credit card then pay using RIA",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pay your registration fee using a sponsorship',	
				'Done' =>'Done',	
				'Youhave-madethe-full-payment' => 'You have made the full payment',	
				'Send-Request' => 'Send Request',	
				'Pay-the-full-registration-feewith' => 'Pay the full registration fee with',	
				'Pay-a-little-amount-With' => 'Pay a little amount with',	
				'&-rest-later' => '& rest later',	
				'Transaction-Details' => 'Transaction Details',	
				'Order-ID' => 'Order ID',	
				'You' => 'You',	
				'Your-Sponsor' => 'Your Sponsor',	
				'Donation' => 'Donation',	
				'No Transactions Found' => 'No Transactions Found',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Thank you for submitting your profile for review. We'll update you soon.",	
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
				
				
				'email_required' => 'Email is required',	
				'email_email' => 'Please Enter Valid email address',	
				'password_required' => 'Password field is required',	
				'password_confirmed_required' => 'Confirmed Password field is required',	
				'password_confirmed' => 'Password must be same as confirm password',	
				'terms_and_condition_required' => 'Terms and Condition field is required',	
				'first_name_required' => 'First Name field is required',	
				'last_name_required' => 'Last Name field is required',	
				'language_required' => 'Language field is required',	
				'last_name_string' => 'Last Name field is required as string',	
				'token_required' => 'Token is required',	
				'name_required' => 'Name field is required',	
				'mobile_required' => 'Mobile field is required',	
				'mobile_numeric' => 'Mobile field must be a numeric',	
				'message_required' => 'Message field is required',	
				'phonecode_required' => 'Phone code field is required',	
				'is_group_required' => 'Is group field is required',	
				'user_whatsup_code_required' => 'User whatsup code field is required',	
				'contact_whatsapp_number_required' => 'Contact whatsapp number field is required',	
				'user_mobile_code_required' => 'User mobile code field is required',	
				'contact_business_number_required' => 'Contact business number field is required',
				'contact_business_number_unique' => 'The business number has already been taken',	
				'is_spouse_required' => 'Is spouse field is required',	
				'is_spouse_registered_required' => 'Is spouse registered field is required',	
				'id_required' => 'Id field is required',	
				'gender_required' => 'Gender field is required',	
				'email_unique' => 'The email has already been taken.',	
				'date_of_birth_required' => 'Date of birth field is required',	
				'date_of_birth_date' => 'Date of birth field is must be a date formate',	
				'citizenship_required' => 'Citizenship field is required',	
				'salutation_required' => 'Salutation field is required',	
				'room_required' => 'Room field is required',	
				'old_password_required' => 'Old password field is required',	
				'new_password_required' => 'New password field is required',	
				'confirm_password_required' => 'Confirm password field is required',	
				'marital_status_required' => 'Marital status field is required',	
				'contact_address_required' => 'Contact address field is required',	
				'contact_zip_code_required' => 'Contact zip code field is required',	
				'contact_country_id_required' => 'Contact country id field is requiredd',	
				'contact_state_id_required' => 'Contact state id field is required',	
				'contact_city_id_required' => 'Contact city id field is required',	
				'user_mobile_code_required' => 'User mobile code field is required',	
				'contact_business_codenumber_required' => 'Contact business code  field is required',	
				'whatsapp_number_same_mobile_required' => 'Whatsapp number same as mobile number  field is required',	
				'contact_state_name_required' => 'Contact state name  field is required',	
				'contact_city_name' => 'Contact city name  field is required',	
				'ministry_address' => 'Ministry address  field is required',	
				'ministry_zip_code' => 'Ministry zip code  field is required',	
				'ministry_country_id' => 'Ministry country id  field is required',	
				'ministry_state_id' => 'Ministry state id  field is required',	
				'ministry_city_id' => 'Ministry city id  field is required',	
				'ministry_pastor_trainer' => 'Ministry pastor trainer  field is required',	
				'ministry_state_name' => 'Ministry state name  field is required',	
				'ministry_city_name' => 'Ministry city name  field is required',	
				'non_formal_trainor' => 'Non formal trainor  field is required',	
				'informal_personal' => 'Informal personal field is required',	
				'howmany_pastoral' => 'How many pastoral field is required',	
				'comment_required' => 'Comment field is required',	
				'willing_to_commit' => 'Willing to commit field is required',	
				'pastorno' => 'Pastorno field is required',		
				'arrival_flight_number' => 'Arrival flight number field is required',	
				'arrival_start_location' => 'Arrival start location field is required',	
				'arrival_date_departure' => 'Arrival date departure field is required',	
				'arrival_date_arrival' => 'Arrival date arrival field is required',	
				'departure_flight_number' => 'Departure flight number field is required',	
				'departure_start_location' => 'Departure start location field is required',	
				'departure_date_departure' => 'Departure date departure field is required',	
				'departure_date_arrival' => 'Departure date arrival field is required',	
				'logistics_dropped' => 'Logistics dropped field is required',	
				'logistics_picked' => 'Logistics picked field is required',	
				'spouse_arrival_flight_number' => 'Spouse arrival flight number field is required',	
				'spouse_arrival_start_location' => 'Spouse arrival start location field is required',	
				'spouse_arrival_date_departure' => 'Spouse arrival date departure field is required',	
				'spouse_arrival_date_arrival' => 'Spouse arrival date arrival field is required',	
				'spouse_departure_flight_number' => 'Spouse departure flight number field is required',	
				'spouse_departure_start_location' => 'Spouse departure start location field is required',	
				'spouse_departure_date_departure' => 'Spouse departure date departure field is required',	
				'spouse_departure_date_arrival' => 'Spouse departure date arrival field is required',	
				'status_required_validation' => 'Status field is required',	
				'remark_required_validation' => 'remark field is required',	
				'session_id' => 'Session id field is required',	
				'session_date' => 'Session date field is required',	
				'session_required_validation' => 'Session field is required',	
				'amount_required' => 'Amount field is required',	
				'amount_numeric' => 'Amount must be a numeric',	
				'mode_required' => 'Mode field is required',	
				'reference_number' => 'Reference number field is required',	
				'country_of_sender' => 'Country of sender field is required',	
				'type' => 'Type field is required',	
				'user_id' => 'user id field is required',	
				'amount_lesser_than' => 'Please select amount lesser than maximum payment',	
				'Your_submission_has_been_sent' => 'Your submission has been sent successfully.',	
				'howmany_futurepastor'=> 'How many future pastor field is required',
				'order_required'=> 'order',
				'countries_which_require_authorized'=> 'List of countries which require Authorized or Stamped Visa',
				'requirements_for_authorized_and_stamped_visa'=> 'requirements for Authorized and Stamped Visa',
				'Passport_Number_already_exists'=> 'Passport Number already exists',
				'Invitetion_send_successfully'=> 'Invitetion send successfully',
				'mode_in'=> 'This payment type not support',
				
				
	
				
			);
		}
		
		
		return $data[$word];
	}

	public static function userMailTrigger($userId,$message,$subject){
		
		$data=new \App\Models\UserMailTrigger();
		$data->user_id = $userId;
		$data->subject = $subject;
		$data->message = $message;
		$data->save();

		
	}

	
	public static function send($to,$data){

		$api_key=env('FIREBASE_TOKEN');
		$url="https://fcm.googleapis.com/fcm/send";
		$fields=json_encode(array('to'=>$to,'notification'=>$data));
	
		// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($fields));
	
		$headers = array();
		$headers[] = 'Authorization: key ='.$api_key;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
	}

	public static function sendPaymentTriggeredMailSend($id,$amount) {
		
		$result = \App\Models\User::where('id',$id)->first();
		if($result){

			$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);
			$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($id, true);
			$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($id, true);
			$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($id, true);
			
			if($result->language == 'sp'){

				$subject = 'Pago recibido. ¡Gracias!';
				$msg = '<p>Estimado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Se ha recibido la cantidad de $'.$amount.' en su cuenta.  </p><p><br></p>
						<p>Gracias por hacer este pago.</p><p> <br></p>
						<p>Le notificaremos tan pronto como el pago sea aprobado en nuestro sistema. Hasta entonces, este pago se reflejará como Pago en proceso.</p><p> <br></p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p>
						<p>IMPORTE TOTAL A PAGAR:'.$result->amount.'</p>
						<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
						<p style="color"red"><i>POR FAVOR, TENGA EN CUENTA: Para poder aprovechar el descuento por “inscripción anticipada”, el pago en su totalidad tiene que recibirse a más tardar el 31 de mayo de 2023.</i></p><p><br></p>
						<p style="color"red"><i>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</i></p><p><br></p>
						<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
						<p>Atentamente,</p>
						<p>El equipo del GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				$subject = 'Paiement intégral.  Merci !';
				$msg = '<p>Cher '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
				<p>Un montant de '.$amount.'$ a été reçu sur votre compte.  </p><p><br></p>
				<p>Merci d’avoir effectué ce paiement.</p><p> <br></p>
				<p>Nous vous informerons dès que le paiement sera approuvé dans notre système. Jusqu’à ce moment-là, ce paiement apparaîtra comme un paiement en cours de traitement.</p>
				<p>Voici un résumé de l’état de votre paiement :</p>
				<p>MONTANT TOTAL À PAYER:'.$result->amount.'</p>
				<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p>
				<p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p>
				<p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
				<p style="color"red"><i>VEUILLEZ NOTER : Afin de bénéficier de la réduction de « l’inscription anticipée », le paiement intégral doit être reçu au plus tard le 31 mai 2023.</i></p><p><br></p>
				<p style="color"red"><i>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</i></p><p><br></p>
				<p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p>
				<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
				<p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

			}elseif($result->language == 'pt'){
			
				$subject = 'Pagamento recebido. Obrigado!';
				$msg = '<p>Prezado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Uma quantia de $'.$amount.' foi recebido na sua conta.  </p><p><br></p>
						<p>Obrigado por ter efetuado esse pagamento.</p><p> <br></p>
						<p>Iremos notificá-lo assim que o pagamento for aprovado no nosso sistema. Até lá, este pagamento reflectirá como Pagamento em Processo.</p><p> <br></p>
						<p>Aqui está o resumo do estado do seu pagamento:</p>
						<p>VALOR TOTAL A SER PAGO:'.$result->amount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
						<p ><i>POR FAVOR NOTE: A fim de poder beneficiar do desconto de "adiantamento", o pagamento integral deve ser recebido até 31 de Maio de 2023.</i></p><p><br></p>
						<p ><i>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor  previamente pagos por si serão retidos.</i></p><p><br></p>
						<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p>
						<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p>
						<p><br></p><p>Calorosamente,</p>
						<p>A Equipe do II CongressoGPro</p>';

			}else{
			
				$subject = 'Payment received. Thank you!';
				$msg = '<p>Dear '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>An amount of $'.$amount.' has been received on your account.  </p><p><br></p>
						<p>Thank you for making this payment.</p><p> <br></p>
						<p>We will notify you as soon as the payment is approved in our system. Until then, this payment will reflect as Payment in Process.</p><p> <br></p>
						<p>Here is a summary of your payment status:</p>
						<p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p>
						<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p>
						<p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p>
						<p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
						<p ><i>PLEASE NOTE: In order to qualify for the “early bird” discount, full payment must be received on or before May 31, 2023</i></p><p><br></p>
						<p ><i>PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</i></p><p><br></p>
						<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p>
						<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
						<p>Warmly,</p>
						<p>GProCongress II Team</p>';

			}

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg,  $subject,);
		}		

	}

	public static function sendMailMadeByTheSponsorIsApproved($orderId) {
		
		$result = \App\Models\SponsorPayment::where('order_id',$orderId)->first();
		
		if($result){
			$user = \App\Models\User::where('id',$result->user_id)->first();

			$subject = 'Thank you for your payment!';
			$msg = '<p>Dear '.$result->name.' ,&nbsp;</p><p><br></p>
					<p>Thank you for sponsoring '.$user->name.' '.$user->last_name.' to attend GProCongress II. </p><p><br></p>
					<p>A payment has been received from you in the amount of $'.$result->amount.' has been approved.  </p><p> <br></p>
					<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p><p> <br></p>
					<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p><p> <br></p>
					<p>Warmly,</p>
					<p>GProCongress II Team</p>';

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

		}		

	}

	public static function sendMailMadeByTheSponsorIsDeclined($orderId) {
		
		$result = \App\Models\SponsorPayment::where('order_id',$orderId)->first();
		
		if($result){
			$user = \App\Models\User::where('id',$result->user_id)->first();

			$token = md5(rand(1111,4444));
			$result->token = $token;
			$result->save();
			
			$url = '<a href="'.url('sponsor-payment-link/'.$token) .'" >click here</a>';

			$subject = 'Your payment has been declined.';
			$msg = '<p>Dear '.$result->name.' ,&nbsp;</p><p><br></p>
					<p>Thank you for sponsoring '.$user->name.' '.$user->last_name.' to attend GProCongress II. </p><p><br></p>
					<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link : '.$url.'</p><p> <br></p>
					<p>If you need to speak to one of our team members, simply reply to this email.</p><p> <br></p>
					<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p><p> <br></p>
					<p>Warmly,</p>
					<p>GProCongress II Team</p>';

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

		}		

	}

	public static function sendSponsorPaymentTriggeredToUserMail($id,$amount,$sponsorName) {
		
		$result = \App\Models\User::where('id',$id)->first();
		if($result){

			$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);
			$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($id, true);
			$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($id, true);
			$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($id, true);
			
			if($result->language == 'sp'){

				$subject = 'Pago recibido del patrocinador '.$sponsorName.'. ¡Gracias!';
				$msg = '<p>Estimado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Se ha recibido una cantidad de $'.$amount.' en su cuenta de su patrocinador '.$sponsorName.'.</p><p><br></p>
						<p>Le notificaremos tan pronto como el pago sea aprobado en nuestro sistema. Hasta entonces, este pago se reflejará como Pago en proceso.</p><p> <br></p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p>
						<p>IMPORTE TOTAL A PAGAR:'.$result->amount.'</p>
						<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
						<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
						<p>Atentamente,</p>
						<p>El equipo del GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				$subject = 'Paiement reçu du '.$sponsorName.'. Merci !';
				$msg = '<p>Cher '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Un montant de $'.$amount.' a été reçu sur votre compte de la part de votre '.$sponsorName.' . </p><p><br></p>
						<p>Nous vous informerons dès que le paiement sera approuvé dans notre système. Jusqu’à ce moment-là, ce paiement apparaîtra comme un paiement en cours de traitement.</p>
						<p>Voici un résumé de l’état de votre paiement :</p>
						<p>MONTANT TOTAL À PAYER:'.$result->amount.'</p>
						<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p>
						<p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p>
						<p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
						<p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p>
						<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
						<p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

			}elseif($result->language == 'pt'){
			
				$subject = 'Pagamento recebido do patrocinador '.$sponsorName.'. Obrigado!';
				$msg = '<p>Prezado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Um montante de $'.$amount.' foi recebido na sua conta do seu patrocinador '.$sponsorName.'.</p><p><br></p>
						<p>Iremos notificá-lo assim que o pagamento for aprovado no nosso sistema. Até lá, este pagamento reflectirá como Pagamento em Processo.</p><p> <br></p>
						<p>Aqui está o resumo do estado do seu pagamento:</p>
						<p>VALOR TOTAL A SER PAGO:'.$result->amount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
						<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p>
						<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p>
						<p><br></p><p>Calorosamente,</p>
						<p>A Equipe do II CongressoGPro</p>';

			}else{
			
				$subject = 'Payment received from sponsor '.$sponsorName.'. Thank you!';
				$msg = '<p>Dear '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>An amount of $'.$amount.' has been received on your account from your sponsor '.$sponsorName.'.  </p><p><br></p>
						<p>We will notify you as soon as the payment is approved in our system. Until then, this payment will reflect as Payment in Process.</p><p> <br></p>
						<p>Here is a summary of your payment status:</p>
						<p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p>
						<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p>
						<p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p>
						<p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
						<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p>
						<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
						<p>Warmly,</p>
						<p>GProCongress II Team</p>';

			}

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg,  $subject,);
		}		

	}

	public static function sendExhibitorsPaymentTriggeredToUserMail($id,$amount,$name) {
		
		$result = \App\Models\User::where('id',$id)->first();
		if($result){

			if($result->language == 'sp'){

				$subject = '¡Su pago ha sido recibido!';
				$msg = '<p>Estimado  '.$name.',</p>
				<p>Hemos recibido un pago suyo por valor de 800 USD.  Gracias por el pago.  El importe correspondiente a su participación como exhibidor en el GProCongress II ha sido pagado en su totalidad.</p>
				<p>Adjuntamos a este correo electrónico una carta de patrocinio para que usted la entregue a quienes vengan de su organización a fin de ayudarlos a obtener sus visas para viajar a Panamá.</p>
				<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
				<p><i>Ore con nosotros para que se multiplique la cantidad y calidad de capacitadores de pastores.</i></p>
				<p>Cordialmente,</p>
				<p>Equipo GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				$subject = 'Votre paiement a été reçu!';
				$msg = '<p>Cher  '.$name.',</p>
				<p>Un paiement de $'.$amount.'. a été reçu de votre part.  Nous vous remercions pour votre paiement.  Votre compte exposant GProCongress II a maintenant été payé en totalité.</p>
				<p>Nous joignons à cet e-mail une lettre de parrainage, que vous pouvez donner à quiconque venant de votre organisation, pour les aider à obtenir leurs visas pour voyager au Panama.</p>
				<p>Si vous avez des questions, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à ce courriel.</p>
				<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</i></p>
				<p>Cordialement,</p>
				<p>L’équipe GProCongress II</p>';

			}elseif($result->language == 'pt'){
			
				$subject = 'Seu pagamento foi recebido!';
				$msg = '<p>Caro '.$name.',</p>
				<p>Um pagamento foi recebido de você no valor de $'.$amount.'. Obrigado por seu pagamento. Sua conta de expositor do GProCongresso II foi paga integralmente.</p>
				<p>Estamos anexando a este e-mail uma carta de patrocínio, para você entregar a quem vier de sua organização, para auxiliá-los na obtenção de seus vistos para viajar ao Panamá</p>
				<p>Se você tiver alguma dúvida ou precisar falar com um dos membros de nossa equipe, basta responder a este e-mail.</p>
				<p><i>Ore conosco para multiplicar a quantidade e qualidade de pastores-treinadores.</i></p>
				<p>Calorosamente,</p>
				<p>Equipe GProCongresso II</p>';

			}else{
			
				$subject = 'Your payment has been received!';
				$msg = '<p>Dear '.$name.',</p>
				<p>A payment has been received from you in the amount of $'.$amount.'.  Thank you for your payment.  Your GProCongress II exhibitor account has now been paid in full.</p>
				<p>We are attaching to this email a sponsorship letter, for you to give to whoever is coming from your organization, to assist them in getting their visas for travel to Panama.</p>
				<p>If you have any questions, or if you need to speak to one of our team members, simply reply to this email.</p>
				<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p>
				<p>Warmly,</p>
				<p>The GProCongress II Team</p>';

			}

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg,  $subject,);
		}		

	}

	public static function sendSponsorPaymentApprovedToUserMail($id,$amount,$type,$order_id) {
		
		$result = \App\Models\User::where('id',$id)->first();
		if($result){

			$sponsorName='';
			$SponsorPayment = \App\Models\SponsorPayment::where('order_id',$order_id)->first();
			if($SponsorPayment){
				$sponsorName= $SponsorPayment->name;
			}
			$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);
			$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($id, true);
			$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($id, true);
			$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($id, true);
			
			if($result->language == 'sp'){

				if($type == 'full'){
					$paymentType = 'Gracias por hacer este pago.';
					$EB = '';
				}else{
					$paymentType = 'Gracias por hacer este pago parcial.';
					$EB = '<p style="color"red"><i>POR FAVOR, TENGA EN CUENTA: Para poder aprovechar el descuento por “inscripción anticipada”, el pago en su totalidad tiene que recibirse a más tardar el 31 de mayo de 2023.</i></p><p><br></p>
					<p style="color"red"><i>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</i></p><p><br></p>';

				}

				$subject = 'Se aprueba el pago recibido del patrocinador '.$sponsorName.'. ¡Gracias!';
				$msg = '<p>Estimado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Se ha aprobado en su cuenta un monto de $'.$amount.' pagado por su patrocinador '.$sponsorName.'.</p><p><br></p>
						<p>Le notificaremos tan pronto como el pago sea aprobado en nuestro sistema. Hasta entonces, este pago se reflejará como Pago en proceso.</p><p> <br></p>
						<p>'.$paymentType.'</p><p> <br></p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p>
						<p>IMPORTE TOTAL A PAGAR:'.$result->amount.'</p>
						<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
						'.$EB.'
						<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
						<p>Atentamente,</p>
						<p>El equipo del GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				if($type == 'full'){
					$paymentType = 'Vous avez maintenant payé la somme totale pour le GProCongrès II.  Merci !';
					$EB = '';
				}else{
					$paymentType = 'Merci d’avoir effectué ce paiement partiel.';
					$EB = '<p style="color"red"><i>VEUILLEZ NOTER : Afin de bénéficier de la réduction de « l’inscription anticipée », le paiement intégral doit être reçu au plus tard le 31 mai 2023.</i></p><p><br></p>
					<p style="color"red"><i>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</i></p><p><br></p>';

				}
				$subject = 'Le paiement reçu du '.$sponsorName.' est approuvé. Merci de votre compréhension.';
				$msg = '<p>Cher '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Un montant de $'.$amount.' payé par votre '.$sponsorName.'  a été approuvé sur votre compte.</p><p><br></p>
						<p>'.$paymentType.'</p><p> <br></p>
						<p>Nous vous informerons dès que le paiement sera approuvé dans notre système. Jusqu’à ce moment-là, ce paiement apparaîtra comme un paiement en cours de traitement.</p>
						<p>Voici un résumé de l’état de votre paiement :</p>
						<p>MONTANT TOTAL À PAYER:'.$result->amount.'</p>
						<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p>
						<p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p>
						<p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
						'.$EB.'
						<p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p>
						<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
						<p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

			}elseif($result->language == 'pt'){
			
				if($type == 'full'){
					$paymentType = 'Você agora pagou na totalidade para o II CongressoGPro. Obrigado!';
					$EB = '';
				}else{
					$paymentType = 'Obrigado por ter efetuado esse pagamento parcial.';
					$EB = '<p style="color"red"><i>POR FAVOR NOTE: A fim de poder beneficiar do desconto de "adiantamento", o pagamento integral deve ser recebido até 31 de Maio de 2023.</i></p><p><br></p>
					<p style="color"red"><i>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor  previamente pagos por si serão retidos.</i></p><p><br></p>';

				}

				$subject = 'Pagamento recebido do patrocinador '.$sponsorName.' está aprovado. Obrigado!.';
				$msg = '<p>Prezado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Um montante de $'.$amount.' pago pelo seu patrocinador '.$sponsorName.' foi aprovado na sua conta.</p><p><br></p>
						<p>Iremos notificá-lo assim que o pagamento for aprovado no nosso sistema. Até lá, este pagamento reflectirá como Pagamento em Processo.</p><p> <br></p>
						<p>'.$paymentType.'</p><p> <br></p>
						<p>Aqui está o resumo do estado do seu pagamento:</p>
						<p>VALOR TOTAL A SER PAGO:'.$result->amount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
						'.$EB.'
						<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p>
						<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p>
						<p><br></p><p>Calorosamente,</p>
						<p>A Equipe do II CongressoGPro</p>';

			}else{
			
				if($type == 'full'){
					$paymentType = 'You have now paid in full for GProCongress II.  Thank you!';
					$EB = ' ';
				}else{
					$paymentType = 'Thank you for partial payment.';
					
					$EB = '<p style="color"red"><i>PLEASE NOTE: In order to qualify for the “early bird” discount, full payment must be received on or before May 31, 2023</i></p><p><br></p>
					<p style="color"red"><i>PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</i></p><p><br></p>';
					
				}

				$subject = 'Payment received from sponsor '.$sponsorName.' is approved. Thank you!';
				$msg = '<p>Dear '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>An amount of $'.$amount.' paid by your sponsor '.$sponsorName.' has been approved on your account. </p><p><br></p>
						<p>We will notify you as soon as the payment is approved in our system. Until then, this payment will reflect as Payment in Process.</p><p> <br></p>
						<p>'.$paymentType.'</p><p> <br></p>
						<p>Here is a summary of your payment status:</p>
						<p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p>
						<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p>
						<p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p>
						<p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
						'.$EB.'
						<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p>
						<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
						<p>Warmly,</p>
						<p>GProCongress II Team</p>';

			}

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg,  $subject,);
		}		

	}
	
	public static function sendSponsorPaymentDeclinedToUserMail($id,$amount,$order_id) {
		
		$result = \App\Models\User::where('id',$id)->first();
		if($result){

			$sponsorName='';
			$SponsorPayment = \App\Models\SponsorPayment::where('order_id',$order_id)->first();
			if($SponsorPayment){
				$sponsorName= $SponsorPayment->name;
			}
			$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($id, true);
			$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($id, true);
			$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($id, true);
			$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($id, true);
			
			if($result->language == 'sp'){

				$subject = 'El pago recibido del patrocinador '.$sponsorName.'. ha sido rechazado ¡Gracias!';
				$msg = '<p>Estimado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>El pago reciente por la cantidad de $'.$amount.' realizado por su patrocinador '.$sponsorName.' ha sido rechazado a GProCongress.</p><p><br></p>
						<p>Le notificaremos tan pronto como el pago sea aprobado en nuestro sistema. Hasta entonces, este pago se reflejará como Pago en proceso.</p><p> <br></p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p>
						<p>IMPORTE TOTAL A PAGAR:'.$result->amount.'</p>
						<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
						<p style="color:red"><i>POR FAVOR, TENGA EN CUENTA: Para poder aprovechar el descuento por “inscripción anticipada”, el pago en su totalidad tiene que recibirse a más tardar el 31 de mayo de 2023.</i></p><p><br></p>
						<p style="color:red"><i>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</i></p><p><br></p>
						<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
						<p>Atentamente,</p>
						<p>El equipo del GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				$subject = 'Le paiement reçu du '.$sponsorName.' est refusé. Nous vous remercions de votre attention.';
				$msg = '<p>Cher '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Le paiement récent d’un montant de '.$amount.' $ effectué par votre '.$sponsorName.'  à GProCongress a été refusé.</p><p><br></p>
						<p>Nous vous informerons dès que le paiement sera approuvé dans notre système. Jusqu’à ce moment-là, ce paiement apparaîtra comme un paiement en cours de traitement.</p>
						<p>Voici un résumé de l’état de votre paiement :</p>
						<p>MONTANT TOTAL À PAYER:'.$result->amount.'</p>
						<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p>
						<p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p>
						<p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
						<p style="color:red"><i>VEUILLEZ NOTER : Afin de bénéficier de la réduction de « l’inscription anticipée », le paiement intégral doit être reçu au plus tard le 31 mai 2023.</i></p><p><br></p>
						<p style="color:red"><i>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</i></p><p><br></p>
						<p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p>
						<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
						<p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

			}elseif($result->language == 'pt'){
			
				$subject = 'Pagamento recebido do patrocinador '.$sponsorName.' está recusado. Obrigado!';
				$msg = '<p>Prezado  '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>O pagamento recente da quantia $'.$amount.' feito pelo seu patrocinador '.$sponsorName.' ao GProCongress foi recusado.</p><p><br></p>
						<p>Iremos notificá-lo assim que o pagamento for aprovado no nosso sistema. Até lá, este pagamento reflectirá como Pagamento em Processo.</p><p> <br></p>
						<p>Aqui está o resumo do estado do seu pagamento:</p>
						<p>VALOR TOTAL A SER PAGO:'.$result->amount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
						<p ><i>POR FAVOR NOTE: A fim de poder beneficiar do desconto de "adiantamento", o pagamento integral deve ser recebido até 31 de Maio de 2023.</i></p><p><br></p>
						<p ><i>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor  previamente pagos por si serão retidos.</i></p><p><br></p>
						<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p>
						<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p>
						<p><br></p><p>Calorosamente,</p>
						<p>A Equipe do II CongressoGPro</p>';

			}else{
			
				$subject = 'Payment received from sponsor '.$sponsorName.' is declined. Thank you!';
				$msg = '<p>Dear '.$result->name.' '.$result->last_name.' ,&nbsp;</p><p><br></p>
						<p>Recent payment of amount $'.$amount.' made by your sponsor '.$sponsorName.' to GProCongress was declined.</p><p><br></p>
						<p>We will notify you as soon as the payment is approved in our system. Until then, this payment will reflect as Payment in Process.</p><p> <br></p>
						<p>Here is a summary of your payment status:</p>
						<p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p>
						<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p>
						<p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p>
						<p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
						<p ><i>PLEASE NOTE: In order to qualify for the “early bird” discount, full payment must be received on or before May 31, 2023</i></p><p><br></p>
						<p ><i>PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</i></p><p><br></p>
						<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p>
						<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
						<p>Warmly,</p>
						<p>GProCongress II Team</p>';

			}

			\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg,  $subject,);
		}
	}		
	
	public static function sendSponsorshipLetterMailSend($user_id,$id) {
		
		$passportApprove= \App\Models\PassportInfo::where('id',$id)->first();

		$user= \App\Models\User::where('id',$user_id)->first();
		$name = $user->name.' '.$user->last_name;

		$url = '<a href="'.url('sponsorship-letter-approve').'">Click here</a>';
		$to = $user->email;

		$subject = 'Please verify your sponsorship letter.';
		$msg = '<p>Thank you for submitting your sponsorship letter.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';

		\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'sponsorship information completed');
		
		if($user->language == 'sp'){

			$subject = "Por favor, verifique su información de viaje";
			$msg = '<p>Estimado '.$name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su información de viaje.&nbsp;</p><p><br></p><p>A continuación, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la información recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: '.$url.' para verificar que la información es correcta.</p><p><br></p><p>Gracias por su colaboración.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
		
		}elseif($user->language == 'fr'){
		
			$subject = "Veuillez vérifier vos informations de voyage";
			$msg = "<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d’avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons rédigée basée sur les informations reçues.&nbsp;</p><p><br></p><p>Pourriez-vous s’il vous plaît examiner la lettre, puis cliquer sur ce lien: '.$url.' pour vérifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe du GProCongrès II</p><div><br></div>";

		}elseif($user->language == 'pt'){
		
			$subject = "Por favor verifique sua Informação de Viagem";
			$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos por submeter sua informação de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informação que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e daí clicar neste link: '.$url.' para verificar que a informação esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p><div><br></div>';
		
		}else{
		
			$subject = 'Please verify your sponsorship letter.';
			$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
								
		}

		$pdf = \PDF::loadView('email_templates.sponsorship_info_show', $passportApprove->toArray());
		$pdf->setPaper('L');
		$pdf->output();
		$fileName = strtotime("now").rand(11,99).'.pdf';
		$path = public_path('uploads/file/');
		$passportApprove->sponsorship_letter=$fileName;
		$passportApprove->save();
		
		$pdf->save($path . '/' . $fileName);
		
		// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
		// \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		
	}

	public static function sendFinancialLetterMailSend($user_id,$id,$financial) {
		
		$passportApprove= \App\Models\PassportInfo::where('id',$id)->first();

		$user= \App\Models\User::where('id',$user_id)->first();
		
		$to = $user->email;

		$subject = 'Financial letter.';

		$rajiv_richard = '<img src="'.asset('images/rajiv_richard.png').'">';

		$msg = '<p>Dear '.$passportApprove->salutation.' '.$passportApprove->name.',</p><p><br></p><p><br></p>
		<p>Passport Number: '.$passportApprove->passport_no.'</p><p><br></p>
		<p>Country: '.\App\Helpers\commonHelper::getCountryNameById($passportApprove->citizenship).'</p><p><br></p>
		<p>This letter will confirm that your application to GProCongress II has been accepted, and that you are invited to attend the Congress in Panama City, Panama, from November 12-17, 2023.</p><p><br></p>
		<p>RREACH is providing you with significant financial assistance, so that you can attend the Congress. First, your registration fee has been discounted to ___________. (RREACH’s cost for each delegate to attend the Congress is around $1,750.00.) In addition, RREACH will cover the following expenses: </p><p><br></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Airport transfers to/from the hotel.</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	A room for five nights at the Westin Playa Bonita Panama Hotel.</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	All meals during the Congress; and</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	All materials for the Congress.</p><p><br></p>
		<p>Therefore, the only costs you are responsible for are your registration fee listed above, and your airfare to Panama. All other incidental expenses and visa fee (if applicable) will be borne by you.</p><p><br></p>
		<p>This is a unique opportunity for you to <b>connect with like-minded peers</b>, who serve undertrained pastors everywhere, and to <b>build new relationships</b> with current and emerging leaders in pastor training.  Secondly, it’s a wonderful time for you to <b>reflect</b> on your ministry calling, to <b>envision</b> the next season of your life and ministry, to <b>think</b> strategically about implementation, and to assess your ministry effectiveness. Thirdly, it’s a great place for you to <b>find resources</b> for your future ministry – learn how to receive <b>strength from the Lord Jesus Christ</b> to continue your work, find committed partners for mutual encouragement in coming years; and gather <b>proven ideas and new models</b> in pastor training partnerships, support, and delivery.</p><p><br</p>
		<p>We hope that you will come to Panama this November, and be part of this global gathering of pastor trainers, designed to enhance pastoral health and advance church health in 200+ nations, and ultimately to reach the <b>next billion individuals</b> with spiritual health.  </p><p><br></p>
		<p>For the glory of Christ, and for the beauty of His Bride,</p><p><br></p>
		<p>'.$rajiv_richard.'</p><p><br></p>
		<p>Rajiv Richard</p>
		<p>GProCongress II Coordinator</p>
		<p>Email: info@gprocongress.org</p>';

		$passportApproveArray= [
			'salutation'=>$passportApprove->salutation,
			'name'=>$passportApprove->name,
			'passport_no'=>$passportApprove->passport_no,
			'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($passportApprove->citizenship),
			'rajiv_richard'=>$rajiv_richard,
			'amount'=>$user->amount,
		];

		
		$pdf = \PDF::loadView('email_templates.financial_letter',$passportApproveArray);
		$pdf->setPaper('L');
		$pdf->output();
		$fileName = $passportApprove->name.'_financial_letter_'.strtotime("now").rand(0000000,9999999).'.pdf';
		$path = public_path('uploads/file/');
		
		$pdf->save($path . '/' . $fileName);

		$passportApproveArray= [
			'salutation'=>$passportApprove->salutation,
			'name'=>$passportApprove->name,
			'passport_no'=>$passportApprove->passport_no,
			'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($passportApprove->citizenship),
			'rajiv_richard'=>$rajiv_richard,
			'amount'=>$user->amount,
		];

		$pdf = \PDF::loadView('email_templates.financial_sp_letter',$passportApproveArray);
		$pdf->setPaper('L');
		$pdf->output();
		$financialSpanishLetter = $passportApprove->name.'_financial_letter_'.strtotime("now").rand(0000000,9999999).'.pdf';
		$path = public_path('uploads/file/');
		
		$pdf->save($path . '/' . $financialSpanishLetter);

		$passportApprove->financial_letter=$fileName;
		$passportApprove->financial_spanish_letter=$financialSpanishLetter;
		if($financial == 'financial'){

			$passportApprove->status='Approve';
		}
		
		$passportApprove->save();

		// $files = [
        //     public_path('uploads/file/'.$fileName),
        //     public_path('uploads/file/'.$financialSpanishLetter),
        // ];

		// \Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files) {
		// 	$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
		// 	$message->subject($subject);
		// 	$message->to($to);
			
		// 	foreach ($files as $file){
        //         $message->attach($file);
        //     }
			
		// });
		
		// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
		// \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		// \App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'financial information completed');
		
		
	}

	public static function sendExhibitorsRegistrationMailSend($id,$password) {
		
		$user = \App\Models\User::where('id',$id)->first();

		$to = $user['email'];

		$subject = "Thank you for registering as an Exhibitor for GProCongress II.";
		$msg = '<p>Dear '.$user->name.' '.$user->salutation.',</p>
				<p>We have received your submission, asking to be an Exhibitor at GProCongress II in Panama this November.  Thank you for your interest in being a part of this important and historic event!</p>
				<p>Your registered email and password are:</p>
				<p><br>Email: '.$to.'<br>Password: '.$password.'<br></p>
				<p>Our team will review your submission, and will be in touch with you soon to confirm whether or not you have been approved.  If you have any questions, or if you need to speak to one of our team members, simply reply to this email.</p>
				<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers.</i> </p>
				<p>Warmly,</p><p><br></p>
				<p>The GProCongress II Team</p>';
		
		\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

		\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id, $subject, $msg, 'Registering as an Exhibitor for GProCongress II.');
		
	}

	public static function sendExhibitorsRegistrationUserHistory($data){
		
		$ExhibitorsData=new \App\Models\Exhibitors();
		
		$ExhibitorsData->user_id = $data['user_id'];
		$ExhibitorsData->business_owner_id = $data['business_owner_id'];
		$ExhibitorsData->parent_id = $data['parent_id'];
		$ExhibitorsData->added_as = $data['added_as'];
		$ExhibitorsData->name = $data['name'];
		$ExhibitorsData->email = $data['email']; 
		$ExhibitorsData->mobile = $data['mobile']; 
		$ExhibitorsData->citizenship = $data['citizenship'];
		$ExhibitorsData->business_name = $data['business_name'];
		$ExhibitorsData->business_identification_no = $data['business_identification_no'];
		$ExhibitorsData->website = $data['website'];
		$ExhibitorsData->dob = $data['dob']; 
		$ExhibitorsData->gender = $data['gender']; 
		$ExhibitorsData->passport_number = $data['passport_number'];  
		$ExhibitorsData->any_one_coming_with_along  = $data['any_one_coming_with_along'];
		$ExhibitorsData->coming_with_spouse  = $data['coming_with_spouse'];
		$ExhibitorsData->last_name  = $data['salutation'];
		$ExhibitorsData->phone_code  = $data['mobile_code'];
		$ExhibitorsData->passport_copy = $data['passport_copy'];
		$ExhibitorsData->diplomatic_passport = $data['diplomatic_passport'];
		$ExhibitorsData->logo = $data['logo'];
		$ExhibitorsData->room = $data['room'];

		$ExhibitorsData->save();
		
		
	}

	
	public static function sendExhibitorSponsorshipLetterMailSend($user_id) {
		
		$passportApprove= \App\Models\Exhibitors::where('user_id',$user_id)->first();

		$user= \App\Models\User::where('id',$user_id)->first();
		$name = $user->name.' '.$user->last_name;

		$url = '<a href="'.url('sponsorship-letter-approve').'">Click here</a>';
		$to = $user->email;

		$subject = 'Please verify your sponsorship letter.';
		$msg = '<p>Thank you for submitting your sponsorship letter.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';

		\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'sponsorship information completed');
		
		if($user->language == 'sp'){

			$subject = "Por favor, verifique su información de viaje";
			$msg = '<p>Estimado '.$name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su información de viaje.&nbsp;</p><p><br></p><p>A continuación, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la información recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: '.$url.' para verificar que la información es correcta.</p><p><br></p><p>Gracias por su colaboración.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
		
		}elseif($user->language == 'fr'){
		
			$subject = "Veuillez vérifier vos informations de voyage";
			$msg = "<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d’avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons rédigée basée sur les informations reçues.&nbsp;</p><p><br></p><p>Pourriez-vous s’il vous plaît examiner la lettre, puis cliquer sur ce lien: '.$url.' pour vérifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe du GProCongrès II</p><div><br></div>";

		}elseif($user->language == 'pt'){
		
			$subject = "Por favor verifique sua Informação de Viagem";
			$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos por submeter sua informação de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informação que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e daí clicar neste link: '.$url.' para verificar que a informação esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p><div><br></div>';
		
		}else{
		
			$subject = 'Please verify your sponsorship letter.';
			$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
								
		}

		$pdf = \PDF::loadView('email_templates.sponsorship_info_show', $passportApprove->toArray());
		$pdf->setPaper('L');
		$pdf->output();
		$fileName = strtotime("now").rand(11,99).'.pdf';
		$path = public_path('uploads/file/');
		$passportApprove->sponsorship_letter=$fileName;
		$passportApprove->save();
		
		$pdf->save($path . '/' . $fileName);
		
		// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
		// \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		
	}

	public static function sendExhibitorFinancialLetterMailSend($user_id) {
		
		$passportApprove= \App\Models\Exhibitors::where('user_id',$user_id)->first();

		$user= \App\Models\User::where('id',$user_id)->first();
		
		$to = $user->email;

		$fileName = 'visa_financial_spanish_letter.pdf';
		$financialSpanishLetter = 'visa_financial_english_letter.pdf';
		
		$passportApprove->financial_letter=$fileName.','.$financialSpanishLetter;
		
		$passportApprove->save();

		// $files = [
        //     public_path('uploads/file/'.$fileName),
        //     public_path('uploads/file/'.$financialSpanishLetter),
        // ];

		// \Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files) {
		// 	$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
		// 	$message->subject($subject);
		// 	$message->to($to);
			
		// 	foreach ($files as $file){
        //         $message->attach($file);
        //     }
			
		// });
		
		// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
		// \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		// \App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'financial information completed');
		
		
	}

	
	public static function sendExhibitorPaymentReminderMailSend($id) {
		
		$result = \App\Models\User::where('id',$id)->first();

		$to = $result->email;
		$website = '<a href="'.url('/payment').'">website</a>';
				
		if($result){

			if($result->language == 'sp'){

				$subject = 'RECORDATORIO: Por favor, realice hoy el pago de su inscripción como exhibidor en el GProCongress II.';
				$msg = '<p>Estimado  '.$result->name.' '.$result->last_name.',&nbsp;</p>
				<p>Su pago de $800 USD por su asistencia como Exhibidor en el GProCongress II vence ahora en su totalidad, pero aún no hemos recibido su pago. Por favor, realice su pago hoy.</p>
				<p>Le recordamos que los exhibidores se eligen por orden de llegada, “primero en pagar, primero en entrar”. En consecuencia, si espera demasiado para realizar el pago, podría quedar fuera del Congreso como exhibidor, debido a que todos los cupos de exhibidores ya podrían estar llenos.</p>
				<p>Puede pagar su tarifa de exhibición de $800 USD en nuestro sitio web '.$website.', o en nuestra aplicación (ENLACE), usando cualquier tarjeta de crédito permitida.</p>
				<p>Si tiene alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
				<p><i>Ore con nosotros para que se multiplique la cantidad y calidad de capacitadores de pastores.<i></p>
				<p>Cordialmente,</p>
				<p>&nbsp;Equipo GProCongress II</p>';

			}elseif($result->language == 'fr'){
			
				$subject = 'RAPPEL – Veuillez payer vos frais d’exposition GProCongress II aujourd’hui.';
				$msg = '<p>Cher  '.$result->name.' '.$result->last_name.',&nbsp;</p>
				<p>Votre paiement de 800 USD pour votre participation en tant qu’exposant au GProCongress II est maintenant dû en totalité, mais nous n’avons pas encore reçu votre paiement.  Veuillez effectuer votre paiement dès aujourd’hui.</p>
				<p>Nous vous rappelons que les exposants sont choisis selon le principe du « premier à payer, premier arrivé ».  Par conséquent, si vous attendez trop longtemps pour effectuer le paiement, vous pourriez être exclu du Congrès en tant qu’exposant, car tous les créneaux d’exposants pourraient déjà être pris.</p>
				<p>Vous pouvez payer vos frais d’exposition de 800 USD sur notre site Web '.$website.'. ou sur notre application (LIEN), en utilisant n’importe quelle carte de crédit majeure. </p>
				<p>Si vous avez des questions sur votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à ce courriel.</p>
				<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.<i></p>
				<p>Cordialement,</p>
				<p>&nbsp;L’équipe GProCongress II</p>';

			}elseif($result->language == 'pt'){
			
				$subject = 'Assunto – LEMBRETE – Por favor, pague sua taxa de exibição do GProCongresso II hoje';
				$msg = '<p>Caro  '.$result->name.' '.$result->last_name.',&nbsp;</p>
				<p>Seu pagamento de $800 USD por sua participação como Expositor no GProCongresso II está vencido integralmente, mas ainda não recebemos seu pagamento. Por favor, faça seu pagamento hoje.</p>
				<p>Lembramos que os expositores são escolhidos na base do “primeiro a pagar, primeiro a chegar”. Assim, se você esperar muito para efetuar o pagamento, poderá ficar de fora do Congresso como expositor, pois todas as vagas de expositor já poderão estar preenchidas.</p>
				<p>Você pode pagar sua taxa de exibição de $ 800 USD em nosso site '.$website.'. ou em nosso aplicativo (LINK), usando qualquer cartão de crédito.</p>
				<p>Se você tiver alguma dúvida sobre como efetuar seu pagamento ou se precisar falar com um dos membros de nossa equipe, basta responder a este e-mail.</p>
				<p><i>Ore conosco para multiplicar a quantidade e qualidade de pastores-treinadores.<i></p>
				<p>Calorosamente,</p>
				<p>&nbsp;Equipe GProCongresso II</p>';

			}else{
			
				$subject = 'REMINDER – Please pay your GProCongress II exhibition fee today.';
				$msg = '<p>Dear '.$result->name.' '.$result->last_name.',&nbsp;</p>
				<p>Your payment of $800 USD for your attendance as an Exhibitor at GProCongress II is now due in full, but we have not yet received your payment.  Please make your payment today.</p>
				<p>We would remind you that exhibitors are chosen on a “first pay, first come” basis.  Accordingly, if you wait too long to make payment, you could be left out of the Congress as an exhibitor, because all exhibitor slots could already be full.</p>
				<p>You may pay your $800 USD exhibition fee on our website '.$website.'. or on our app (LINK), using any major credit card.</p>
				<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
				<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers<i></p>
				<p>Warmly,</p>
				<p>&nbsp;The GProCongress II Team</p>';

			}
			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
			\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg, 'REMINDER – Please make your GProCongress II payment today.');
	
		}
	}

	public static function countExhibitorPaymentSuccess(){
		
		$result = \App\Models\Exhibitors::where('payment_status','Success')->count();
		if($result <= 10){
			return true;
		}else{
			return false;
		}
	}

}

?>