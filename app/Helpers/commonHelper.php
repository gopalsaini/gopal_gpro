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
		$payment->currency_id='233';
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


}


?>