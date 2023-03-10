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
			$msg = '<p>Estimado '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aqu?? tiene un resumen actual del estado de su pago: '.$result->amount.'</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a m??s tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripci??n quedar?? sin efecto y se ceder?? su lugar a otra persona.</p><p><br></p><p>??Tiene preguntas? Simplemente responda a este correo electr??nico y nuestro equipo estar?? encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p><p>Para realizar el pago ingrese a <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p><p>Para mayor informaci??n vea el siguiente tutorial https://youtu.be/xSV96xW_Dx0 </p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p><div><br></div>';
		
		}elseif($result->language == 'fr'){
		
			$subject = "Paiement du solde GProCongr??s II: EN ATTENTE";
			$msg = "<p>Cher ".$result->name." ".$result->last_name.",&nbsp;</p><p><br></p><p>Nous vous ??crivons pour vous rappeler que vous avez des paiements en attente pour r??gler le solde d?? sur votre compte GProCongr??s II.&nbsp;&nbsp;</p><p>Voici un r??sum?? de l?????tat de votre paiement : '.$result->amount.'</p><p><br></p><p>MONTANT TOTAL ?? PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS EFFECTU??S ANT??RIEUREMENT ET ACCEPT??S : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p><p>SOLDE RESTANT D?? : ".$totalPendingAmount."</p><p><br></p><p>Veuillez payer le solde au plus tard le&nbsp; 31st August 2023.&nbsp;</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n???est pas re??u avant le 31st August 2023, votre inscription sera annul??e et votre place sera donn??e ?? quelqu???un d???autre.&nbsp;</p><p><br></p><p>Avez-vous des questions ? R??pondez simplement ?? cet e-mail et notre ??quipe sera heureuse d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier le nombre et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br><p><br></p><p>Pour effectuer le paiement, veuillez vous rendre sur <a href='https://www.gprocongress.org/payment' traget='blank'> www.gprocongress.org/payment </a> </p><p>Pour plus d` informations, regardez le tutoriel https://youtu.be/xSV96xW_Dx0 </p> </p><p>Cordialement,</p><div><br></div>";

		}elseif($result->language == 'pt'){
		
			$subject = "Pagamento do Saldo PENDENTE para o II CongressoGPro";
			$msg = '<p>Prezado '.$result->name.'  '.$result->last_name.',&nbsp;</p><p><br></p><p>Estamos escrevendo para lhe lembrar que tem pagamentos pendentes para regularizar o seu saldo em d??vida na sua conta para o II CongressoGPro.&nbsp;&nbsp;</p><p><br></p><p>Aqui est?? o resumo do estado atual do seu pagamento: '.$result->amount.'</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITO : '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM ABERTO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor pague o saldo at?? o dia ou antes de 31st August 2023.</p><p><br></p><p>POR FAVOR NOTE: Se seu pagamento n??o for recebido at?? o dia 31st August 2023, a sua inscri????o ser?? cancelada, e a sua vaga ser?? atribu??da a outra pessoa.</p><p><br></p><p>Alguma d??vida? Simplesmente responda a este e-mail, e nossa equipe estar?? muito feliz para entrar em contacto com voc??.&nbsp;</p><p><br></p><p>Ore conosco a medida em que nos esfor??amos para multiplicar os n??meros e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><p><br></p><p>Para fazer o pagamento, favor ir par <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p><p>Para mais informa????es, veja o tutorial https://youtu.be/xSV96xW_Dx0 </p></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';
		
		}else{
		
			$subject = 'PENDING: Balance payment for GProCongress II';
			$msg = '<div>Dear '.$name.',&nbsp;</div><div><br></div><div>We are writing to remind you that you have pending payments to settle the balance due on your GProCongress II account.&nbsp;&nbsp;</div><div><br></div><div>Here is a summary of your payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID: '.$result->amount.'</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div> Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><p>To make the payment please go to <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p><p>For more information watch the tutorial https://youtu.be/xSV96xW_Dx0 </p></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
			
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
			'Strat??ge' => 'Strategist',
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
			'' => '',
		);
		
		return $data[$id];
	}

	public static function ApiMessageTranslaterLabel($lang,$word){
		
		if($lang == 'sp'){

			$data=array(
				'Please-select-YesNo' => 'Por favor, seleccione S?? o NO',
				'Please-Group-Users' => 'Por favor, agregue el correo electr??nico de los usuarios del grupo.',
				'Wehave-duplicate-email-group-users' => "Hemos encontrado un correo electr??nico duplicado en los usuarios del Grupo.",
				'isalready-exist-please-use-another-email-id' => 'ya existe con nosotros, as?? que use otra identificaci??n de correo electr??nico',
				"Wehave-found-duplicate-mobile-Group-users" => "Hemos encontrado n??mero de tel??fono m??vil duplicado en usuarios del grupo.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'ya existe con nosotros, as?? que use otro n??mero de tel??fono m??vil.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "Hemos encontrado un n??mero de whatsApp duplicado en los usuarios de grupo.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'Este n??mero de WhatsApp ya existe con nosotros, as?? que use otro n??mero.',
				'GroupInfo-updated-successfully' => 'La Informaci??n del grupo ha sido actualizada con ??xito.',
				'Spouse-not-found' => 'C??nyuge no encontrado',
				'Spouse-already-associated-withother-user' => 'C??nyuge ya asociado con otro usuario',
				'Youhave-already-updated-spouse-detail' => 'Ya ha actualizado los datos del c??nyuge',
				'DateOfBirthyear-mustbemore-than-18years' => 'La fecha del a??o de nacimiento debe ser m??s de 18 a??os',
				'Spouse-added-successful'=>'C??nyuge agregado con ??xito.',
				'Spouse-update-successful' => 'Actualizaci??n de c??nyuge exitosa',
				'Stay-room-update-successful' => 'Actualizaci??n exitosa de la habitaci??n ',
				'NewPassword-update-successful' => 'Nueva actualizaci??n de contrase??a exitosa',
				'Profile-updated-successfully' => 'perfil actualizado con ??xito',
				'Something-went-wrongPlease-try-again' => 'Algo sali?? mal. Por favor, vuelva a intentarlo',
				'Contact-Details-updated-successfully' => 'Detalles del contacto actualizados con ??xito.',
				'Youare-not-allowedto-update-profile' => 'No se le permite actualizar perfil',
				'Pastor-detail-not-found' => 'No se encuentra datos del pastor',
				'Profile-details-submit-successfully' => 'Datos del perfil enviados correctamente.(Detalles del perfil Enviar correctamente)',
				'Please-verify-ministry-details' => 'Por favor, verifique los detalles del ministerio',
				'Ministry-Pastor-detail-updated-successfully' => 'Dato del pastor del ministerio actualizado con ??xito.',
				'Your-travelinfo-hasbeenalready-added' => 'Su informaci??n de viaje ya ha sido agregada',
				'Travel-Info-Submittedsuccesfully' => 'Informaci??n de viaje enviada con ??xito',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Su informaci??n de viaje se ha enviado con ??xito',
				'Please-verify-yourtravel-information' => 'Por favor, verifique su informaci??n de viaje',
				'Travel-information-hasbeen-successfully-completed' => 'La informaci??n de viaje se ha completado con ??xito',	
				'Your-travelInfo-has-been-verified-successfully' => 'Su informaci??n de viaje ha sido verificada con ??xito',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Su carta preliminar de visa ha sido verificada con ??xito. Carta de visa preliminar verificada con ??xito',	
				'TravelInfo-doesnot-exist' => 'La informaci??n de viaje no existe',	
				'TravelInformation-remarksubmit-successful' => 'El comentario (Observaci??n) de informaci??n de viaje se ingreso con (Enviar) exitoso.',	
				'Youarenot-allowedto-updateTravelInformation' => 'No se le permite actualizar la informaci??n de viaje',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Su sesi??n se ha agregado con ??xito',	
				'Session-information-hasbeen-successfully-completed' => 'La informaci??n de la sesi??n se ha completado con ??xito.',	
				'Sessioninfo-doesnot-exists' => 'La informaci??n de la sesi??n no existe',	
				'Session-information-hasbeen-successfullyverified' => 'La informaci??n de la sesi??n se ha verificado con ??xito',	
				'Youarenot-allowedto-updatesession-information' => 'No se le permite actualizar la informaci??n de la sesi??n',	
				'Payment-Linksent-succesfully' => 'Enlace de pago enviado con ??xito',	
				'Payment-Link' => 'Enlace de pago',	
				'Payment-Successful' => 'Pago exitoso',	
				'Transaction-already-exists' => 'La transacci??n ya existe',	
				'Transaction-hasbeensent-successfully' => 'La transacci??n ha sido enviada con ??xito',	
				'Requestor-Payment-is-completed' => 'El pago del solicitante ha sido completado',	
				'Offline-payment-successful' => 'Pago fuera de l??nea exitoso',	
				'Data-not-available' => 'Informacion no disponible',	
				'payment-added-successful' => 'pago agregado con exito ',	
				'No-payment-due' => 'Sin deuda pendiente',	
				'Visa-letter-info-doesnot-exist' => 'La informaci??n de la carta de Visa no existe',	
				'Visaletter-file-fetche-succesully' => 'Archivo de carta de visa obtenido con ??xito',	
				'Notification-fetched-successfully' => 'Notificaci??n obtenida con ??xito',	
				'Emailalready-existsPlease-trywithanother-emailid' => 'El correo electr??nico ya existe. Por favor, intente con otro correo electr??nico',	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => 'El enlace de verificaci??n de correo electr??nico se ha enviado correctamente a su direcci??n de correo electr??nico.',	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => 'Su registro se ha completado con ??xito. Por favor, actualice su perfil',	
				'Email-already-verifiedPlease-Login' => 'Se ha verificado el correo electr??nico, por favor, inicie sesi??n',	
				'This-account-doesnot-exist' => 'Esta cuenta no existe',	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => 'Tu cuenta ha sido bloqueada. P??ngase en contacto con el administrador',	
				'Invalid-Password' => 'Contrase??a incorrecta invalida',	
				'Payment-link-hasbeen-expired' => 'El enlace de pago ha expirado',	
				'Payment-Successful' => 'Pago exitoso',	
				'Sponsor-Submitted-Payment' => 'Pago registrado por patrocinador Patrocinador enviado Pago',	
				'Confirmation-link-has-expired' => 'El enlace de confirmaci??n ha sido expirado',	
				'Your-SpouseConfirmation-isRejected!' => 'La confirmaci??n de su c??nyuge ha sido rechazada',	
				'Confirmation-Successful' => 'Confirmaci??n exitosa',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Hemos enviado un enlace de restablecimiento de contrase??a a su direcci??n de correo electr??nico',	
				'Resetpassword-linkhasbeen-expired' => 'El enlace de contrase??a de reinicio ha expirado',	
				'Yourprofile-isunder-review' => 'Su perfil est?? en revisi??n',	
				'Your-Travel-Information-pending' => 'Su informaci??n de viaje pendiente',	
				'YourSession-Informationpending' => 'La informaci??n de su sesi??n pendiente',	
				'Your-application-alreadyApproved' => 'Su solicitud ya aprobada',	
				'Cash-Payment-addedSuccessful' => 'Pago en efectivo agregado con ??xito',	
				'TravelInformation-approved-successful' => 'Informaci??n de viaje aprobada con ??xito',	
				'Travel-Information-notApproved' => 'Informaci??n de viaje no aprobada',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si no puede pagar con su tarjeta de cr??dito, entonces pague con RAI",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pague su matricula de inscripci??n utilizando un patrocinio',	
				'Done' =>'realizado',	
				'Youhave-madethe-full-payment' => 'Has realizado el pago completo',	
				'Send-Request' => 'Enviar petici??n',	
				'Pay-the-full-registration-feewith' => 'Pagar la matricula de inscripci??n completa con',	
				'Pay-a-little-amount-With' => 'Pagar una peque??a cantidad con',	
				'&-rest-later' => 'y el saldo restante m??s tarde',	
				'Transaction-Details' => 'Detalles de la transacci??n',	
				'Order-ID' => 'Numero de solicitud',	
				'You' => 'Usted',	
				'Your-Sponsor' => 'Su patrocinador',	
				'Donation' => 'Donaci??n',	
				'No Transactions Found' => 'No se encontraron transacciones',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Gracias por enviar su perfil para revisi??n. Le actualizaremos pronto.",	
				"Account-Rejected" => "Cuenta rechazada",	
				"Sorry-youraccount-didntpassour-verificationsystem" => "Lo sentimos, su cuenta no ha pasado nuestro sistema de verificaci??n",	
				"Vie-Details" => "Ver detalles",	
				'Your-SpouseDetails' => 'Detalles de su c??nyuge',	
				'Not-Available' => 'No disponible',	
				'Nothing-Found' => 'No se ha encontrado nada',	
				"You-dont-haveanytravel-informationPlease" => "No tiene informaci??n de viaje. Agregue su informaci??n de viaje para verla y administrarla aqu??",	
				'No-SessionAvailable' => 'No hay sesi??n disponible',	
				'Something-happenedplease-tryagainlater' => 'Algo sucedio, int??ntelo de nuevo m??s tarde',	
				'Please-submityourprofile-dataOnour-website' => 'Antes de iniciar sesi??n, introduzca los datos de su perfil en nuestro sitio web.',	
				"Youve-successfullyoffline" => "Ha enviado correctamente el pago fuera de l??nea para su revisi??n",	
				"Your-paymentwas-successful" => "El pago se ha realizado correctamente",	
				'Please-checkinputs&try-again' => 'Verifique las entradas e int??ntelo de nuevo',

				
				
				'email_required' => 'La direcci??n de correo electr??nico es obligatoria',	
				'email_email' => 'Introduzca una direcci??n de correo electr??nico v??lida',	
				'password_required' => 'El campo Contrase??a es obligatorio',	
				'password_confirmed_required' => 'Confirmed Password field is required',
				'password_confirmed' => 'La contrase??a debe ser la misma que la contrase??a de confirmaci??n',	
				'terms_and_condition_required' => 'El campo T??rminos y Condiciones es obligatorio',	
				'first_name_required' => 'El campo Nombre es obligatorio',	
				'last_name_required' => 'El campo Apellido es obligatorio',	
				'language_required' => 'El campo Idioma es obligatorio',	
				'last_name_string' => 'Se requiere el campo Apellido como secuencia',	
				'token_required' => 'Se requiere c??digo',	
				'name_required' => 'El campo Nombre es obligatorio',	
				'mobile_required' => 'El campo M??vil es obligatorio',	
				'mobile_numeric' => 'El campo M??vil debe ser num??rico',	
				'message_required' => 'El campo Mensaje es obligatorio',	
				'phonecode_required' => 'El campo C??digo de Tel??fono es obligatorio',	
				'is_group_required' => 'Por favor, seleccione S?? o No para el grupo',
				'user_whatsup_code_required' => 'El campo c??digo whatsup del usuario es obligatorio',	
				'contact_whatsapp_number_required' => 'el campo n??mero whatsapp de contacto es obligatorio',	
				'user_mobile_code_required' => 'El campo c??digo m??vil del usuario es obligatorio',	
				'contact_business_number_required' => 'el campo n??mero de contacto es obligatorio',
				'contact_business_number_unique' => 'The business number has already been taken',	
				'is_spouse_required' => '"??Viene con su c??nyuge al Congreso?" es obligatorio',	
				'is_spouse_registered_required' => 'C??nyuge ya inscrito - por favor confirmar',	
				'id_required' => '"Identificaci??n" es obligatorio',	
				'gender_required' => 'el campo sexo es obligatorio',	
				'email_unique' => 'El correo electr??nico ya ha sido tomado',	
				'date_of_birth_required' => 'el campo fecha de nacimiento es obligatorio',	
				'date_of_birth_date' => 'el campo de fecha de nacimiento debe estar en formato de fecha',	
				'citizenship_required' => 'El campo ciudadan??a es obligatorio',	
				'salutation_required' => 'el campo saludo es obligatorio',	
				'room_required' => 'el campo habitaci??n es obligatorio',	
				'old_password_required' => 'el campo contrase??a antigua es obligatorio',	
				'new_password_required' => 'el campo nueva contrase??a es obligatorio',	
				'confirm_password_required' => 'el campo confirmar contrase??a es obligatorio',	
				'marital_status_required' => 'el campo estado civil es obligatorio',	
				'contact_address_required' => 'el campo direcci??n de contacto es obligatorio',	
				'contact_zip_code_required' => 'el campo c??digo postal de contacto es obligatorio',	
				'contact_country_id_required' => 'El campo "Pa??s" es obligatorio',	
				'contact_state_id_required' => 'El campo "Estado/Provincia" es obligatorio',	
				'contact_city_id_required' => 'El campo "Ciudad" es obligatorio',	
				'user_mobile_code_required' => 'el campo c??digo m??vil del usuario es obligatorio',	
				'contact_business_codenumber_required' => 'el campo c??digo de empresa del contacto es obligatorio',	
				'whatsapp_number_same_mobile_required' => 'el campo del numero de whatsapp es igual al numero de movil es obligatorio',	
				'contact_state_name_required' => 'el campo nombre del estado/provincia de contacto es obligatorio',	
				'contact_city_name' => 'el campo del nombre de la ciudad del contacto es obligatorio',	
				'ministry_address' => 'el campo direcci??n del ministerio es obligatorio',	
				'ministry_zip_code' => 'el campo c??digo postal del ministerio es obligatorio',	
				'ministry_country_id' => 'el campo id del pa??s del ministerio es obligatorio',	
				'ministry_state_id' => 'el campo id del estado/provincia del ministerio es obligatorio',	
				'ministry_city_id' => 'el campo id de la ciudad del ministerio es obligatorio',	
				'ministry_pastor_trainer' => 'el campo capacitador de pastores del ministerio es obligatorio',	
				'ministry_state_name' => 'el campo nombre del estado/provincia del ministerio es obligatorio',	
				'ministry_city_name' => 'el campo del nombre de la ciudad del ministerio es obligatorio',	
				'non_formal_trainor' => 'Por favor, seleccione una opci??n en "Capacitaci??n Pastoral No Formal"',	
				'informal_personal' => 'Por favor, seleccione una opci??n en "Mentoreo Personal Informal"',
				'howmany_pastoral' => 'Por favor, seleccione una opci??n en "??Con cu??ntos l??deres est?? usted involucrado en fortalecer cada a??o?"',
				'comment_required' => 'el campo comentario es obligatorio',	
				'willing_to_commit' => 'el campo "??Est?? dispuesto a comprometerse en formar a un capacitador de pastores al a??o durante los pr??ximos 7 a??os?" es obligatorio.',	
				'pastorno' => 'Por favor, seleccione una opci??n en "??Cu??ntos de ellos pueden servir como futuros capacitadores de pastores?"',	
				'arrival_flight_number' => 'el campo n??mero de vuelo de llegada es obligatorio',	
				'arrival_start_location' => 'el campo ubicaci??n de inicio de llegada es obligatorio',	
				'arrival_date_departure' => 'el campo fecha salida de llegada es obligatorio',	
				'arrival_date_arrival' => 'el campo de la fecha de llegada es obligatorio',	
				'departure_flight_number' => 'el campo n??mero de vuelo de salida es obligatorio',	
				'departure_start_location' => 'el campo lugar de salida es obligatorio',	
				'departure_date_departure' => 'el campo fecha de salida del regreso es obligatorio',	
				'departure_date_arrival' => 'el campo fecha de llegada del regreso es obligatorio',	
				'logistics_dropped' => 'Por favor, seleccione S?? o No para "??Le gustar??a a usted y a su c??nyuge que el Gpro Congress les llevara de vuelta al aeropuerto?"',	
				'logistics_picked' => 'Por favor, seleccione S?? o No para "??Le gustar??a a usted y a su c??nyuge que el GproCongress les recogiera en el aeropuerto?"',
				'spouse_arrival_flight_number' => 'el campo n??mero de vuelo de llegada del c??nyuge es obligatorio',	
				'spouse_arrival_start_location' => 'el campo lugar de salida del c??nyuge es obligatorio',	
				'spouse_arrival_date_departure' => 'el campo fecha de llegada salida del c??nyuge es obligatorio',	
				'spouse_arrival_date_arrival' => 'el campo fecha de llegada del conyuge es obligatorio',	
				'spouse_departure_flight_number' => 'El N??mero de Vuelo es obligatorio',	
				'spouse_departure_start_location' => 'El lugar de Salida es obligatorio',	
				'spouse_departure_date_departure' => 'Seleccione la Fecha de Salida del C??nyuge',	

				'spouse_departure_date_arrival' => 'Spouse departure date arrival field is required',	

				'status_required_validation' => 'el campo estado es obligatorio',	
				'remark_required_validation' => 'el campo observaci??n es obligatorio',	
				'session_id' => 'El ID de la Sesi??n es obligatorio',	
				'session_date' => 'La Fecha de la Sesi??n es obligatoria',	
				'session_required_validation' => 'El Nombre de la Sesi??n es obligatorio',	
				'amount_required' => 'el campo importe es obligatorio',	
				'amount_numeric' => 'el campo importe debe ser num??rico',	
				'mode_required' => 'el campo modo es obligatorio',	
				'reference_number' => 'el campo n??mero de referencia es obligatorio',	
				'country_of_sender' => 'el campo pa??s del remitente es obligatorio',	
				'type' => 'el campo tipo es obligatorio',	
				'user_id' => 'el campo ID de usuario es obligatorio',	
				'amount_lesser_than' => 'Seleccione un importe inferior al pago m??ximo',	
				'Your_submission_has_been_sent' => 'Su env??o se ha realizado correctamente.',	
				'howmany_futurepastor'=> 'Por favor, seleccione una opci??n en "??Cu??ntos de ellos pueden servir como futuros capacitadores de pastores?"',
				'order_required'=> 'order',
				
			);

		}elseif($lang == 'fr'){

			$data=array(
				'Please-select-YesNo' => 'Veuillez s??lectionner Oui ou Non',
				'Please-Group-Users' => "Veuillez ajouter l'adresse e-mail de l'utilisateur du groupe",
				'Wehave-duplicate-email-group-users' => "Nous avons trouv?? un double de l'e-mail des utilisateurs du groupe.",
				'isalready-exist-please-use-another-email-id' => 'existe d??j?? avec nous, veuillez utiliser une autre adresse e-mail.',
				"Wehave-found-duplicate-mobile-Group-users" => "Nous avons trouv?? un num??ro de portable en double chez les utilisateurs du groupe.",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'existe d??j?? avec nous, veuillez donc utiliser un autre num??ro de portable.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "We've found duplicate WhatsApp mobile number in Group users.",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'existe d??j?? avec nous, veuillez utiliser un autre num??ro WhatsApp.',
				'GroupInfo-updated-successfully' => 'Les informations de groupe ont ??t?? mises ?? jour avec succ??s.',
				'Spouse-not-found' => 'Conjoint/e introuvable',
				'Spouse-already-associated-withother-user' => 'Conjoint/e d??j?? associ?? ?? un autre utilisateur',
				'Youhave-already-updated-spouse-detail' => 'Vous avez d??j?? mis ?? jour les d??tails du conjoint/e',
				'DateOfBirthyear-mustbemore-than-18years' => 'La date de naissance doit ??tre sup??rieure ?? 18 ans.',
				'Spouse-added-successful' => 'Mise ?? jour du conjoint/e r??ussi',
				'Spouse-update-successful' => 'Ajout du conjoint/e r??ussi',
				'Stay-room-update-successful' => "Mise ?? jour de la chambre d'h??tel a ??t?? r??ussie",
				'NewPassword-update-successful' => 'Mise ?? jour du nouveau mot de passe r??ussie',
				'Profile-updated-successfully' => 'Mise ?? jour du profil r??ussie',
				'Something-went-wrongPlease-try-again' => "Une erreur s'est produite. Veuillez r??essayer",
				'Contact-Details-updated-successfully' => 'Coordonn??es mises ?? jour avec succ??s.',
				'Youare-not-allowedto-update-profile' => "Vous n'??tes pas autoris?? ?? mettre ?? jour le profil",
				'Pastor-detail-not-found' => 'D??tail du pasteur introuvable',
				'Profile-details-submit-successfully' => 'D??tails du profil ont ??t?? soumis avec succ??s',
				'Please-verify-ministry-details' => 'Veuillez v??rifier les d??tails du minist??re',
				'Ministry-Pastor-detail-updated-successfully' => 'Les d??tails du minist??re du pasteur ont ??t?? mis ?? jour avec succ??s.',
				'Your-travelinfo-hasbeenalready-added' => 'Vos informations de voyage ont d??j?? ??t?? ajout??es',
				'Travel-Info-Submittedsuccesfully' => 'Informations de voyage soumises avec succ??s',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'Vos informations de voyage ont ??t?? envoy??es avec succ??s',
				'Please-verify-yourtravel-information' => 'Veuillez v??rifier vos informations de voyage',
				'Travel-information-hasbeen-successfully-completed' => 'Informations de voyage ont ??t?? compl??t??es avec succ??s',	
				'Your-travelInfo-has-been-verified-successfully' => "Vos informations de voyage ont ??t?? v??rifi??es avec succ??s",	
				'Preliminary-Visa-Letter-successfully-verified' => 'Lettre pr??liminaire de visa v??rifi??e avec succ??s',	
				'TravelInfo-doesnot-exist' => "Informations de voyage n'existent pas",	
				'TravelInformation-remarksubmit-successful' => "Vous n'??tes pas autoris?? ?? mettre ?? jour les informations de voyage",	
				'Youarenot-allowedto-updateTravelInformation' => 'Votre session a ??t?? ajout??e avec succ??s',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Informations sur la session ont ??t?? compl??t??es avec succ??s.',	
				'Session-information-hasbeen-successfully-completed' => 'Session information has been successfully completed.',	
				'Sessioninfo-doesnot-exists' => "Informations de session n'existent pas",	
				'Session-information-hasbeen-successfullyverified' => "Informations de session ont ??t?? v??rifi??es avec succ??s",	
				'Youarenot-allowedto-updatesession-information' => "Vous n'??tes pas autoris?? ?? mettre ?? jour les informations de session",	
				'Payment-Linksent-succesfully' => 'Lien de paiement envoy?? avec succ??s',	
				'Payment-Link' => 'Lien de paiement',	
				'Payment-Successful' => 'Paiement r??ussi',	
				'Transaction-already-exists' => 'Transaction existe d??j??',	
				'Transaction-hasbeensent-successfully' => 'Transaction a ??t?? envoy??e avec succ??s',	
				'Requestor-Payment-is-completed' => 'Paiement du demandeur a ??t?? compl??t??',	
				'Offline-payment-successful' => 'Paiement hors ligne r??ussi',	
				'Data-not-available' => 'Donn??es non disponibles',	
				'payment-added-successful' => 'paiement ajout?? r??ussi',	
				'No-payment-due' => "Aucun paiement n'est d??",	
				'Visa-letter-info-doesnot-exist' => "Information sur la lettre de visa n'existe pas",	
				'Visaletter-file-fetche-succesully' => 'Fichier de lettre de visa r??cup??r?? avec succ??s',	
				'Notification-fetched-successfully' => 'Notification r??cup??r??e avec succ??s',	
				'Emailalready-existsPlease-trywithanother-emailid' => "E-mail existe d??j??. Veuillez essayer un autre identifiant d'e-mail.",	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => "Le lien de v??rification de l'e-mail a ??t?? envoy?? avec succ??s sur votre adresse e-mail.",	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => "Votre inscription a ??t?? compl??t??e avec succ??s. Veuillez mettre ?? jour votre profil",	
				'Email-already-verifiedPlease-Login' => 'E-mail d??j?? v??rifi??. Veuillez vous connecter',	
				'This-account-doesnot-exist' => "Ce compte n'existe pas",	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => "Votre compte a ??t?? bloqu??. Veuillez contacter l'administrateur",	
				'Invalid-Password' => 'Mot de passe incorrect',	
				'Payment-link-hasbeen-expired' => 'Lien de paiement a ??t?? expir??',	
				'Payment-Successful' => 'Paiement r??ussi',	
				'Sponsor-Submitted-Payment' => 'Paiement soumis par le sponsor',	
				'Confirmation-link-has-expired' => 'Lien de confirmation a ??t?? expir??',	
				'Your-SpouseConfirmation-isRejected!' => 'La confirmation de votre conjoint/e a ??t?? rejet??e!',	
				'Confirmation-Successful' => 'Confirmation r??ussie',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Nous avons envoy?? un lien de r??initialisation de mot de passe sur votre adresse e-mail',	
				'Resetpassword-linkhasbeen-expired' => 'R??initialiser le lien du mot de passe a ??t?? expir??',	
				'Yourprofile-isunder-review' => "Votre profil est en cours d'examen",	
				'Your-Travel-Information-pending' => 'Vos informations de voyage sont en attente',	
				'YourSession-Informationpending' => 'Vos informations de session sont en attente',	
				'Your-application-alreadyApproved' => 'Votre demande a d??j?? ??t?? approuv??e',	
				'Cash-Payment-addedSuccessful' => 'Paiement en esp??ces ajout?? avec succ??s',	
				'TravelInformation-approved-successful' => 'Informations de voyage approuv??es r??ussies',	
				'Travel-Information-notApproved' => 'Informations de voyage non approuv??es',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Si vous ne parvenez pas ?? payer avec votre carte de cr??dit, payez avec RAI",	
				'Payyour-registrationfee-usinga-sponsorship' => "Payez vos frais d'inscription en utilisant un parrainage",	
				'Done' =>'Termin??',	
				'Youhave-madethe-full-payment' => 'Vous avez effectu?? le paiement complet',	
				'Send-Request' => 'Envoyer une demande',	
				'Pay-the-full-registration-feewith' => "Payer les frais d'inscription complets avec",	
				'Pay-a-little-amount-With' => 'Payer une petite somme avec',	
				'&-rest-later' => '& reposez vous plus tard',	
				'Transaction-Details' => 'D??tails de la transaction',	
				'Order-ID' => 'Num??ro de commande',	
				'You' => 'Vous',	
				'Your-Sponsor' => 'Votre sponsor',	
				'Donation' => 'Don',	
				'No Transactions Found' => 'Aucune transaction trouv??e',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Merci d'avoir soumis votre profil pour examen. Nous vous tiendrons au courant bient??t.",	
				"Account-Rejected" => "Compte rejet??",	
				"Sorry-youraccount-didntpassour-verificationsystem" => "D??sol??, votre compte n'a pas r??ussi notre syst??me de v??rification",	
				"Vie-Details" => "Voir les d??tails",	
				'Your-SpouseDetails' => 'Les d??tails de votre conjoint/e',	
				'Not-Available' => 'Pas disponible',	
				'Nothing-Found' => "Rien n'a ??t?? trouv??",	
				"You-dont-haveanytravel-informationPlease" => "Vous n'avez aucune information de voyage. Veuillez ajouter vos informations de voyage pour les voir et les g??rer ici",	
				'No-SessionAvailable' => 'Aucune session disponible',	
				'Something-happenedplease-tryagainlater' => "Quelque chose s'est produit, veuillez r??essayer plus tard",	
				'Please-submityourprofile-dataOnour-website' => "Veuillez d'abord soumettre vos donn??es de profil sur notre site Web avant de vous connecter",	
				"Youhave-successfullyoffline" => "Vous avez r??ussi ?? soumettre le paiement hors ligne pour examen",	
				"Your-paymentwas-successful" => "Votre paiement a r??ussi",	
				'Please-checkinputs&try-again' => 'Veuillez v??rifier les entr??es et r??essayer',

				
				
				'email_required' => "L'e-mail est exig??",	
				'email_email' => 'Veuillez entrer une adresse e-mail valide',	
				'password_required' => 'Le champ du mot de passe est exig??',	
				'password_confirmed_required' => 'confirmation Le champ du mot de passe est exig??',
				'password_confirmed' => 'Mot de passe doit ??tre le m??me que le mot de passe de confirmation',	
				'terms_and_condition_required' => 'Le champ "Termes et Conditions " est exig??',	
				'first_name_required' => 'Le champ "Pr??nom" est exig??',	
				'last_name_required' => 'Le champ "Nom de famille" est exig??',	
				'language_required' => 'Le champ "Langue" est exig??',	
				'last_name_string' => 'Le champ "Nom de famille" est exig?? en tant que cha??ne de caract??res',	
				'token_required' => 'Un jeton est exig??',	
				'name_required' => 'Le champ "Nom" est exig??',	
				'mobile_required' => 'Le champ "T??l??phone portable" est exig??',	
				'mobile_numeric' => 'Le champ "T??l??phone portable" doit ??tre un num??ro',	
				'message_required' => 'Le champ de"Message" est exig??',	
				'phonecode_required' => 'Le champ "Code t??l??phonique" est exig??',	
				'is_group_required' => 'Veuillez s??lectionner Oui ou Non pour le groupe',	
				'user_whatsup_code_required' => "le champ Code WhatsApp de l'utilisateur est exig??",	
				'contact_whatsapp_number_required' => 'le champ "Num??ro WhatsApp du contact" est exig??',	
				'user_mobile_code_required' => "le champ Code du t??l??phone portable de l'utilisateur est exig??",	
				'contact_business_number_required' => "le champ Num??ro de t??l??phone du travail est exig??",

				'contact_business_number_unique' => 'The business number has already been taken',
					
				'is_spouse_required' => '"Venez-vous avec votre conjoint/e au Congr??s ?" est exig??',	
				'is_spouse_registered_required' => 'Conjoint/e d??j?? inscrit - veuillez confirmer',	
				'id_required' => '"id" est exig??',	
				'gender_required' => 'Le champ "sexe" est exig??',	
				'email_unique' => "L'e-mail a d??j?? ??t?? pris.",	
				'date_of_birth_required' => 'le champ "date de naissance" est exig??',	
				'date_of_birth_date' => 'Le champ "date de naissance" doit ??tre un format de date.',	
				'citizenship_required' => 'le champ "citoyennet??" est exig??',	
				'salutation_required' => 'le champ "salutation" est exig??',	
				'room_required' => 'le champ "chambre" est exig??',	
				'old_password_required' => "le champ de l'ancien mot de passe est exig??",	
				'new_password_required' => 'le champ du "nouveau mot de passe" est exig??',	
				'confirm_password_required' => 'le champ "confirmer le mot de passe" est exig??',	
				'marital_status_required' => 'le champ "??tat civil" est exig??',	
				'contact_address_required' => 'le champ "adresse de contact" est exig??',	
				'contact_zip_code_required' => 'le champ "code postal" du contact est exig??',	
				'contact_country_id_required' => 'le champ "ID du pays" exig??',	
				'contact_state_id_required' => "le champ ID de l' ??tat/Province est exig??",	
				'contact_city_id_required' => 'le champ "Ville"est exig??',	
				'user_mobile_code_required' => "le champ du code du t??l??phone portable de l'utilisateur est exig??",	
				'contact_business_codenumber_required' => 'Le champ du "code du num??ro de t??l??phone du travail" est requis',	
				'whatsapp_number_same_mobile_required' => 'le champ du "num??ro WhatsApp identique au num??ro de portable" est exig??',	
				'contact_state_name_required' => "le champ du nom de l'??tat/province du contact est exig??",	
				'contact_city_name' => 'le champ du "Nom de la ville" du contact est exig??',	
				'ministry_address' => 'le champ "Adresse du minist??re" est exig??',	
				'ministry_zip_code' => 'le champ du "Code postal du minist??re" est exig??',	
				'ministry_country_id' => 'le champ "ID du pays du minist??re" est exig??',	
				'ministry_state_id' => "Le champ de 'ID de l'??tat/province du minist??re' est exig??",	
				'ministry_city_id' => 'Le champ de "ID de la ville du minist??re" est exig??',	
				'ministry_pastor_trainer' => 'le champ de "formateur de pasteur du minist??re" est exig??',	
				'ministry_state_name' => "le champ du 'nom de l'??tat/province du minist??re' est exig??",	
				'ministry_city_name' => 'le champ du "nom de la ville du minist??re" est exig??d',	
				'non_formal_trainor' => 'Veuillez choisir une option dans "Formation pastorale non formelle"',	
				'informal_personal' => 'Veuillez choisir une option dans "Mentorat personnel informel"',	
				'howmany_pastoral' => 'Veuillez s??lectionner une option dans "Combien de responsables pastoraux participez-vous ?? renforcer chaque ann??e ?"',	
				'comment_required' => 'le champ "commentaire" est exig??',	
				'willing_to_commit' => '"??tes-vous pr??t ?? vous engager ?? former un formateur de pasteurs par an pendant les 7 prochaines ann??es ?" est exig??.',	
				'pastorno' => 'le champ de "combien de pasteurs au futur" est exig??',	
				'arrival_flight_number' => "le champ du num??ro du vol d'arriv??e est exig??",	
				'arrival_start_location' => "le champ de l'arriv??e du lieu de d??part est exig??",	
				'arrival_date_departure' => 'le champ de la date d arriv??e d??part est exig??',	
				'arrival_date_arrival' => "le champ de la date d'arriv??e est exig??",	
				'departure_flight_number' => 'le champ du "num??ro de vol de d??part" est exig??',	
				'departure_start_location' => 'Le champ du "lieu de d??part" est exig??',	
				'departure_date_departure' => 'le champ de "la date de d??part" est exig??',	
				'departure_date_arrival' => 'le champ de "la date de d??part arriv??e" est exig??',
				'logistics_dropped' => 'Veuillez s??lectionner Oui ou Non pour "Souhaitez-vous que votre conjoint(e) et vous-m??me soyez d??pos??s par Gpro Congress ?? l a??roport ?"',	
				'logistics_picked' => 'Veuillez s??lectionner Oui ou Non pour Souhaitez-vous que votre conjoint(e) et vous soyez pris(e) en charge par Gpro Congress ?? l a??roport ?',	
				'spouse_arrival_flight_number' => 'le champ du num??ro de vol d arriv??e du conjoint/e est exig??',	
				'spouse_arrival_start_location' => "le champ du'lieu de d??part de l'arriv??e du conjoint/e' est exig??",	
				'spouse_arrival_date_departure' => "le champ de'la date d'arriv??e du conjoint/e' est exig??",	
				'spouse_arrival_date_arrival' => "le champ de'la date d arriv??e du conjoint/e est exig??",	
				'spouse_departure_flight_number' => 'Le num??ro de vol est exig??',	
				'spouse_departure_start_location' => 'Le lieu de d??part est exig??',	
				'spouse_departure_date_departure' => 'Veuillez s??lectionner la date de d??part du conjoint/e',

				'spouse_departure_date_arrival' => 'Spouse departure date arrival field is required',	

				'status_required_validation' => 'le champ "statut" est exig??',	
				'remark_required_validation' => 'le champ "remarque" est exig??',	
				'session_id' => '"ID de la session" est exig??',	
				'session_date' => '"date de la session" est exig??',	
				'session_required_validation' => '"le nom de la session" est exig??',	
				'amount_required' => 'le champ "montant" est exig??',	
				'amount_numeric' => 'Le montant doit ??tre un chiffre',	
				'mode_required' => 'le champ "mode" est exig??',	
				'reference_number' => 'le champ "num??ro de r??f??rence" est exig??',	
				'country_of_sender' => "le champ 'pays de l'exp??diteur' est exig??",	
				'type' => 'le champ "type" est exig??',	
				'user_id' => "le champ 'ID de l'utilisateur' est exig??",	
				'amount_lesser_than' => 'Veuillez s??lectionner un montant inf??rieur au paiement maximum',	
				'Your_submission_has_been_sent' => 'Votre demande a ??t?? envoy??e avec succ??s.',
				'howmany_futurepastor'=> 'le champ de "combien de pasteurs au futur" est exig??',	
				'order_required'=> 'order',
				
			);

		}elseif($lang == 'pt'){

			$data=array(
				
				'Please-select-YesNo' => 'Por favor selecione Sim ou N??o',
				'Please-Group-Users' => 'Por favor, adicione o email do Grupo de Usu??rios.',
				'Wehave-duplicate-email-group-users' => "Encontramos e-mail duplicado no Grupo de usu??rios",
				'isalready-exist-please-use-another-email-id' => 'j?? tem uma conta conosco, por favor use outro e-mail',
				"Wehave-found-duplicate-mobile-Group-users" => "Encontramos numeros de telefone duplicados no Grupo de usu??rios",
				'isAlreadyExist-withusSo-please-use-another-mobile-number' => 'J?? tem uma conta conosco, por favor usar outro n??mero de telefone.',
				"We-have-found-duplicateWhatsAppmobile-numberin-groupusers" => "Encontramos o n??mero de WhatsApp duplicado no Grupo de usu??rios",
				'is-already-existwith-usso-please-use-anotherwhatsapp-number' => 'J?? tem uma conta conosco, por favor use outro n??mero de WhatsApp',
				'GroupInfo-updated-successfully' => 'Informa????es do grupo atualizadas com sucesso',
				'Spouse-not-found' => 'C??njuge n??o encontrado',
				'Spouse-already-associated-withother-user' => 'C??njuge j?? associado a outro usu??rio',
				'Youhave-already-updated-spouse-detail' => 'Voc?? j?? atualizou o detalhe do c??njuge',
				'DateOfBirthyear-mustbemore-than-18years' => 'A data do ano de nascimento deve ser superior a 18 anos',
				'Spouse-added-successful'=>'Adicionou o c??njuge com sucesso',
				'Spouse-update-successful' => 'Atualiza????o do c??njuge bem -sucedido',
				'Stay-room-update-successful' => 'Atualiza????o da Sala de espera feita com sucesso',
				'NewPassword-update-successful' => 'Nova atualiza????o da senha bem -sucedida',
				'Profile-updated-successfully' => 'Perfil atualizado com sucesso',
				'Something-went-wrongPlease-try-again' => 'Algo deu errado. Por favor tente novamente',
				'Contact-Details-updated-successfully' => 'Detalhes de contato atualizados com sucesso.',
				'Youare-not-allowedto-update-profile' => 'Voc?? n??o tem permiss??o para atualizar o perfil',
				'Pastor-detail-not-found' => 'Informacao do Pastor n??o encontrada',
				'Profile-details-submit-successfully' => 'Detalhes do perfil enviados com sucesso',
				'Please-verify-ministry-details' => 'Por favor, verifique os detalhes do minist??rio',
				'Ministry-Pastor-detail-updated-successfully' => 'Informacao detalhada do ministerio do pastor atualizado com sucesso',
				'Your-travelinfo-hasbeenalready-added' => 'Suas informa????es de viagem j?? foram adicionadas',
				'Travel-Info-Submittedsuccesfully' => 'Informa????es de viagem enviadas com sucesso',
				'Your-travelinfo-hasbeen-sendSuccessfully' => 'As suas informa????es de viagem foram enviadas com sucesso',
				'Please-verify-yourtravel-information' => 'Por favor, verifique suas informa????es de viagem',
				'Travel-information-hasbeen-successfully-completed' => 'A informa??ao sobre a viagem foi completada com sucesso',	
				'Your-travelInfo-has-been-verified-successfully' => 'Suas informa????es de viagem foram verificadas com sucesso',	
				'Preliminary-Visa-Letter-successfully-verified' => 'Carta de visto preliminar verificada com sucesso',	
				'TravelInfo-doesnot-exist' => 'Informa????es de viagem n??o existem',	
				'TravelInformation-remarksubmit-successful' => 'Comentario de informa????es de viagem enviado com sucesso',	
				'Youarenot-allowedto-updateTravelInformation' => 'Nao tem permissao para atualizar as informa????es sobre viagem',	
				'Yoursession-hasbeen-addedsuccessfully' => 'Sua sess??o foi adicionada com sucesso',	
				'Session-information-hasbeen-successfully-completed' => 'A informa??ao da sess??o foi realizada com sucesso',	
				'Sessioninfo-doesnot-exists' => 'Informa????es da sess??o n??o existem',	
				'Session-information-hasbeen-successfullyverified' => 'As informa????es da sess??o foram verificadas com sucesso',	
				'Youarenot-allowedto-updatesession-information' => 'Voc?? n??o tem permiss??o para atualizar as informa????es da sess??o',	
				'Payment-Linksent-succesfully' => 'Link de pagamento enviado com sucesso',	
				'Payment-Link' => 'Link de pagamento',	
				'Transaction-already-exists' => 'Transa????o j?? existe',	
				'Transaction-hasbeensent-successfully' => 'A transa????o foi enviada com sucesso',	
				'Requestor-Payment-is-completed' => 'O pagamento do solicitante esta conclu??da',	
				'Offline-payment-successful' => 'Pagamento offline bem-sucedido',	
				'Data-not-available' => 'Dados n??o dispon??veis',	
				'payment-added-successful' => 'pagamento adicionado bem -sucedido',	
				'No-payment-due' => 'Nenhum pagamento pendente',	
				'Visa-letter-info-doesnot-exist' => 'Informa????es da carta de visto n??o existe',	
				'Visaletter-file-fetche-succesully' => 'Arquivo de cartas de visto feito com sucesso',	
				'Notification-fetched-successfully' => 'Notifica????o obtida com sucesso',	
				'Emailalready-existsPlease-trywithanother-emailid' => 'E-mail j?? existe. Por favor, tente com outro e-mail',	
				'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid' => 'O link de verifica????o de email foi enviado com sucesso para o seu e -mail',	
				'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile' => 'Seu registro foi conclu??do com sucesso, por favor atualize seu perfil',	
				'Email-already-verifiedPlease-Login' => 'E -mail j?? verificado, por favor, inicie a sessao',	
				'This-account-doesnot-exist' => 'Essa conta n??o existe',	
				'YourAccount-hasbeenBlocked-Pleasecontact-Administrator' => 'Sua conta foi bloqueada. Por favor contacte o Administrador',	
				'Invalid-Password' => 'Senha Inv??lida',	
				'Payment-link-hasbeen-expired' => 'O link de pagamento expirou',	
				'Payment-Successful' => 'Pagamento efetuado com sucesso',	
				'Sponsor-Submitted-Payment' => 'Patrocinador realizou o pagamento',	
				'Confirmation-link-has-expired' => 'O link de confirma????o expirou',	
				'Your-SpouseConfirmation-isRejected!' => 'A confirma????o do seu c??njuge foi rejeitada!',	
				'Confirmation-Successful' => 'Confirma????o efetuada com sucesso',	
				'WeHave-sentPassword-resetLinkOn-yourEmail-address' => 'Enviamos o link de redefini????o de senha para o seu e-mail',	
				'Resetpassword-linkhasbeen-expired' => 'O link da redifinicao de senha ja expirou',	
				'Yourprofile-isunder-review' => 'Seu perfil est?? sendo revisto',	
				'Your-Travel-Information-pending' => 'Suas informa????es de viagem estao pendentes',	
				'YourSession-Informationpending' => 'Informacoes de sua sessao esta pendente',	
				'Your-application-alreadyApproved' => 'Sua inscri????o j?? foi aprovada',	
				'Cash-Payment-addedSuccessful' => 'Pagamento em especie adicionado com sucesso',	
				'TravelInformation-approved-successful' => 'Informa????es de viagem aprovada com sucesso',	
				'Travel-Information-notApproved' => 'Informa????es de viagem n??o aprovadas',	
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "Se voc?? n??o puder pagar com seu cart??o de cr??dito, pague usando RAI",	
				'Payyour-registrationfee-usinga-sponsorship' => 'Pague sua taxa de inscri????o usando um patroc??nio',	
				'Done' =>'Feito',	
				'Youhave-madethe-full-payment' => 'Voc?? fez o pagamento integral',	
				'Send-Request' => 'Enviar pedido',	
				'Pay-the-full-registration-feewith' => 'Pagar a taxa de inscricao completa ',	
				'Pay-a-little-amount-With' => 'Pague uma pequena quantia com',	
				'&-rest-later' => 'e o restante pague depois',	
				'Transaction-Details' => 'Detalhes da transa????o',	
				'Order-ID' => 'ID do pedido',	
				'You' => 'Voc??',	
				'Your-Sponsor' => 'Seu patrocinador',	
				'Donation' => 'Doa????o',	
				'No Transactions Found' => 'Nenhuma transa????o encontrada',	
				"Thankyoufor-submittingyourprofile-forreviewWe'll-updateyousoon" => "Agradecemos por enviar seu perfil para revis??o. Vamos atualiz?? -lo em breve",	
				"Account-Rejected" => "Conta rejeitada",	
				"Sorry-yourAccount-didntPassOur-verifiCationsystem" => "Desculpe, sua conta n??o passou no sistema de verifica????o",	
				"Vie-Details" => "Ver detalhes",	
				'Your-SpouseDetails' => 'Detalhes do seu c??njuge',	
				'Not-Available' => 'N??o dispon??vel',	
				'Nothing-Found' => 'Nada encontrado',	
				"You-dont-haveanytravel-informationPlease" => "Voc?? n??o tem qualquer informa????o de viagem. Por favor adicione suas informa????es de viagem para a ver e gerencir por aqui",	
				'No-SessionAvailable' => 'Nenhuma sess??o dispon??vel',	
				'Something-happenedplease-tryagainlater' => 'Algo aconteceu, por favor tente novamente mais tarde',	
				'Please-submityourprofile-dataOnour-website' => 'Por favor submeta os seus dados de perfil em nosso site antes de iniciar a sessao',	
				"Youve-successfullyoffline" => "Voc?? enviou com sucesso o pagamento offline para revis??o",	
				"Your-paymentwas-successful" => "Seu pagamento foi efetuado com sucesso",	
				'Please-checkinputs&try-again' => 'Por favor, verifique as entradas e tente novamente',


				'email_required' => 'O e-mail ?? obrigatorio',	
				'email_email' => 'Por favor introduza um e-mail v??lido',	
				'password_required' => 'A Senha ?? obrigat??ria',	
				'password_confirmed_required' => 'A senha deve ser a mesma que a senha de confirmacao',
				'password_confirmed' => 'A senha deve ser a mesma que a senha de confirmacao',	
				'terms_and_condition_required' => 'O campo: Termos e Condi????o ?? obrigat??rio',	
				'first_name_required' => 'O campo: Nome ?? obrigat??rio',	
				'last_name_required' => 'O campo: Sobrenome ?? obrigat??rio',	
				'language_required' => 'O campo: Idiomas ?? obrigat??rio',	
				'last_name_string' => 'O campo: Sobrenome ?? obrigat??rio',	
				'token_required' => 'O Token ?? obrigatorio',	
				'name_required' => 'O campo: Nome ?? obrigat??rio',	
				'mobile_required' => 'Campo: Telefone m??vel ?? obrigatorio',	
				'mobile_numeric' => 'O campo do telefone m??vel deve ser num??rico',	
				'message_required' => 'O campo: Mensagem ?? obrigat??rio',	
				'phonecode_required' => 'O campo: c??digo telef??nico ?? obrigat??rio',	
				'is_group_required' => 'O campo: grupo ?? obrigatorio',	
				'user_whatsup_code_required' => 'campo: c??digo do usuario whatsapp ?? obrigatorio',	
				'contact_whatsapp_number_required' => 'O campo: contato do numero de whatsapp ?? necess??rio',	
				'user_mobile_code_required' => 'o campo: c??digo de telemovel do usuario ?? obrigat??rio',	
				'contact_business_number_required' => 'campo: n??mero da empresa do usuario ?? obrigat??rio',

				'contact_business_number_unique' => 'The business number has already been taken',

				'is_spouse_required' => 'O campo: c??njuge ?? obrigat??rio',	
				'is_spouse_registered_required' => 'O campo: registo de c??njuge ?? obrigat??rio',	
				'id_required' => 'campo: Identificacao ?? necess??rio',	
				'gender_required' => 'o campo: g??nero ?? necess??rio',	
				'email_unique' => 'O e-mail j?? foi recebido.',	
				'date_of_birth_required' => 'O campo: data de nascimento ?? obrigat??rio',	
				'date_of_birth_date' => 'o campo: data de nascimento deve ser um formul??rio de data',	
				'citizenship_required' => 'o campo: cidadania ?? obrigat??rio',	
				'salutation_required' => 'campo: sauda????o ?? necess??rio',	
				'room_required' => 'O campo: quarto ?? necess??rio',	
				'old_password_required' => 'campo: senha antiga ?? obrigat??ria',	
				'new_password_required' => '?? necess??rio um novo campo de senha',	
				'confirm_password_required' => 'O campo: confirmar a senha ?? obrigat??rio',	
				'marital_status_required' => 'o campo: estado civil ?? obrigat??rio',	
				'contact_address_required' => 'o campo: endere??o de contacto ?? obrigat??rio',	
				'contact_zip_code_required' => 'campo: c??digo postal de contato ?? obrigat??rio',	
				'contact_country_id_required' => 'campo: identifica????o do pa??s de contacto ?? obrigat??rio',	
				'contact_state_id_required' => 'campo: identifica????o do estado de contacto ?? necess??rio',	
				'contact_city_id_required' => 'campo: identifica????o da cidade de ?? obrigat??rio',	
				'user_mobile_code_required' => 'o campo: c??digo de telem??vel do usuario ?? obrigat??rio',	
				'contact_business_codenumber_required' => 'campo: c??digo comercial ?? obrigat??rio',	
				'whatsapp_number_same_mobile_required' => 'o que ?? necess??rio para o n??mero de telem??vel',	
				'contact_state_name_required' => 'o campo: nome do estado ?? obrigat??rio',	
				'contact_city_name' => 'o campo: nome da cidade ?? obrigat??rio',	
				'ministry_address' => 'o campo: endere??o do minist??rio ?? obrigat??rio',	
				'ministry_zip_code' => 'o campo: c??digo postal do minist??rio ?? obrigat??rio',	
				'ministry_country_id' => 'o campo: identifica????o do pa??s do minist??rio ?? obrigat??rio',	
				'ministry_state_id' => 'campo: identificacao do estado do minist??rio ?? obrigat??rio',	
				'ministry_city_id' => 'campo: identificacao da cidade do minist??rio ?? obrigat??rio',	
				'ministry_pastor_trainer' => 'o campo: formadores de pastores do minist??rio ?? obrigat??rio',	
				'ministry_state_name' => 'O campo: nome do estado do minist??rio ?? obrigat??rio',	
				'ministry_city_name' => 'O campo: nome da cidade do minist??rio ?? obrigat??rio',	
				'non_formal_trainor' => 'O Campo: Treinador n??o formal ?? obrigat??rio',	
				'informal_personal' => 'O Campo: Treinador formal ?? obrigat??rio',	
				'howmany_pastoral' => 'O Campo: quantos treinadores pastorais ?? obrigat??rio',	
				'comment_required' => 'O campo: coment??rios ?? obrigat??rio',	
				'willing_to_commit' => '?? necess??rio estar disposto a comprometer-se no terreno',	
				'pastorno' => 'O Campo: quantos futuros pastores ?? obrigatorio',
				'arrival_flight_number' => 'o campo: n??mero do voo de chegada ?? obrigat??rio',	
				'arrival_start_location' => 'o campo: localiza????o de in??cio de chegada ?? obrigat??rio',	
				'arrival_date_departure' => 'O Campo: data de chegada ?? obrigat??rio',	
				'arrival_date_arrival' => 'campo: data de chegada ?? obrigat??rio',	
				'departure_flight_number' => 'o campo: n??mero do voo de partida ?? obrigat??rio',	
				'departure_start_location' => 'O campo: local de in??cio de partida ?? obrigat??rio',	
				'departure_date_departure' => 'O campo: data de partida ?? obrigat??rio',	
				'departure_date_arrival' => 'O campo: data de chegada ?? obrigat??rio',	
				'logistics_dropped' => '?? necess??rio escolher o campo log??stico',	
				'logistics_picked' => '?? necess??ria uma log??stica de campo abandonado',	
				'spouse_arrival_flight_number' => 'o campo: n??mero do voo de chegada do c??njuge ?? obrigat??rio',	
				'spouse_arrival_start_location' => 'o campo: localiza????o de in??cio de chegada do c??njuge ?? obrigat??rio',	
				'spouse_arrival_date_departure' => 'O Campo: data de partida do c??njuge ?? obrigat??rio',	
				'spouse_arrival_date_arrival' => 'o campo:: data de chegada do c??njuge ?? obrigat??rio',	
				'spouse_departure_flight_number' => 'o campo: n??mero de voo de partida do c??njuge ?? obrigat??rio',	
				'spouse_departure_start_location' => 'o campo: local de partida do c??njuge ?? obrigat??rio',	
				'spouse_departure_date_departure' => 'campo: data de partida do c??njuge ?? obrigat??rio',	
				'spouse_departure_date_arrival' => 'o campo: data de partida do c??njuge ?? obrigat??rio',	
				'status_required_validation' => 'o campo: estado ?? obrigat??rio',	
				'remark_required_validation' => 'campo: observa????o ?? obrigat??rio',	
				'session_id' => 'o campo: identifica????o da sess??o ?? obrigat??rio',	
				'session_date' => 'o campo: data da sess??o ?? obrigat??rio',	
				'session_required_validation' => 'campo: sess??o ?? necess??rio',	
				'amount_required' => 'campo: quantia ?? necess??rio',	
				'amount_numeric' => 'quantidade deve ser em n??mero',	
				'mode_required' => 'o campo: modo ?? necess??rio',	
				'reference_number' => 'o campo: n??mero de refer??ncia ?? obrigat??rio',	
				'country_of_sender' => 'O campo: pa??s do remetente ?? obrigat??rio',	
				'type' => 'O campo: escrever ?? obrigat??rio',	
				'user_id' => 'O campo: identifica????o do usuario ?? obrigat??rio',	
				'amount_lesser_than' => 'Por favor, selecione um montante inferior ao valor m??ximot',	
				'Your_submission_has_been_sent' => 'A sua submiss??o foi realizada com sucesso',	
				'howmany_futurepastor'=> 'O Campo: quantos futuros pastores ?? obrigatorio',
				'order_required'=> 'order',

				
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
				"Ifyoure-unabletopay-withyourcredit-cardthenpay-usingMoneyGram" => "If you're unable to pay with your credit card then pay using RAI",	
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

}

?>