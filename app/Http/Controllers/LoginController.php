<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use Mail;
use Validator;
use Newsletter;
use Socialite;

class LoginController extends Controller
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

    public function registration(Request $request){

        $data=array(
            'first_name'=>$request->post('first_name'),
            'last_name'=>$request->post('last_name'),
            'language'=>$request->post('language'),
            'email'=>$request->post('email'),
            'password'=>$request->post('password'),
            'password_confirmation'=>$request->post('password_confirmation'),
            'terms_and_condition'=>$request->post('terms_and_condition') == 'on' ? 1 : 0
        );
  
        $result=\App\Helpers\commonHelper::callAPI('POST', '/registration', json_encode($data));
        
        $resultData=json_decode($result->content, true);
       
        if($result->status==200){

            \Session::put('lang', $request->post('language'));

            $html=view('verification', compact('data'))->render();

            return response(array('message'=>$resultData['message'], 'html'=>$html), 200); 

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }
        
    }

    public function sendOtp(Request $request){

        $data=array(
            'email'=>$request->post('email')
        );

        $result=\App\Helpers\commonHelper::callAPI('POST', '/send-otp?lang='.\Session::get('lang'), json_encode($data));
        $resultData=json_decode($result->content, true);

        return response(array('message'=>$resultData['message']), $result->status);

    }

    public function emailRegistrationConfirm(Request $request,$token){

       
        $data=array(
            'token'=>$token,
        );

        $result=\App\Helpers\commonHelper::callAPI('POST', '/send-token?lang='.\Session::get('lang'), json_encode($data));
        
        $resultData=json_decode($result->content, true);

        if($result->status==200){
            
            Session::put('gpro_user', $resultData['token']);
            Session::put('gpro_result', $resultData['result']);
            Session::put('registration_completed', true);

            \Session::flash('gpro_success', $resultData['message']);
            return redirect('groupinfo-update');

            // return response(array('message'=>$resultData['message'], 'token'=>$resultData['token']), $result->status);

        }else{

            if(isset($resultData['langError']) && $resultData['langError'] == true){
               
                \App\Helpers\commonHelper::setLocale();
                \Session::flash('gpro_success', \Lang::get('web/home.Email-already-verifiedPlease-Login') );

            }else{

                \Session::flash('gpro_success', $resultData['message']);
            }
            
            return redirect('/');
            // return response(array('message'=>$resultData['message']), $result->status);
        }

    }

    public function validateOtp(Request $request){

        $otp=$request->post('otp1');
        $otp.=$request->post('otp2');
        $otp.=$request->post('otp3');
        $otp.=$request->post('otp4');

        $data=array(
            'email'=>$request->post('email'),
            'otp'=>$otp
        );

        $result=\App\Helpers\commonHelper::callAPI('POST', '/validate-otp', json_encode($data));
        $resultData=json_decode($result->content, true);

        if($result->status==200){
            
            Session::put('gpro_user', $resultData['token']);
            Session::put('gpro_result', $resultData['result']);
            Session::put('registration_completed', true);

            return response(array('message'=>$resultData['message'], 'token'=>$resultData['token']), $result->status);

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }

    }

    public function login(Request $request){
       
        $data=array(
            'email'=>$request->post('email'),
            'password'=>$request->post('password'),
            'lang'=>\Session::get('lang'),
        );

        $result=\App\Helpers\commonHelper::callAPI('POST', '/login?lang='.\Session::get('lang'), json_encode($data));
        
        $resultData=json_decode($result->content,true);

        if($result->status==200){

            if($resultData['otp_verified']=='Yes'){

                Session::put('gpro_user',$resultData['token']);
                Session::put('gpro_result',$resultData['result']);
                
                return response(array('message'=>$resultData['message'],'otp_verified'=>'Yes', 'token'=>$resultData['token']), $result->status);

            }else{

                $html=view('verification', compact('data'))->render();

                return response(array('message'=>$resultData['message'], 'otp_verified'=>'No', 'html'=>$html), 200); 
            }
            

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }
        
    }

    public function forgotPassword(Request $request){

        if($request->ajax()){

            $data=array(
                'email'=>$request->post('email')
            );

            $result=\App\Helpers\commonHelper::callAPI('POST', '/forgot-password?lang='.\Session::get('lang'), json_encode($data));
           
            $resultData=json_decode($result->content,true);

            if($result->status==200){
    
                \DB::table('password_resets')->insert(['email' => $request->post('email'), 'token' => $resultData['token'], 'created_at' => \Carbon\Carbon::now()]);
                $user = \App\Models\User::where('email', $request->post('email'))->first();
                
                $name = $user->name;
                $to = $request->post('email');
                $token = $resultData['token']; 
                $subject = 'Reset your password';

                if($user->language == 'sp'){

                    $link = '<a href="'.url('reset-password/'.$to.'/'.$token).'">aqui</a>';

                    $subject = "Restablezca su contrase??a.";
                    $msg = '<p><font color="#999999"><span style="font-size: 14px;">Estimado '.$user->name.' '.$user->last_name.',</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">A continuaci??n, encontrar?? un enlace que puede utilizar para restablecer su contrase??a: '.$link.'.</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">??No pidi?? restablecer su contrase??a? ??Tienes alguna otra pregunta? Simplemente responda a este correo electr??nico para hablar con uno de los miembros de nuestro equipo.</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Atentamente,&nbsp;</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">El Equipo GproCongress II</span></font></p><div><br></div>';
                
                }elseif($user->language == 'fr'){
                
                    $link = '<a href="'.url('reset-password/'.$to.'/'.$token).'">aqui</a>';

                    $subject = "R??initialisez votre mot de passe.";
                    $msg = '<p><font color="#999999"><span style="font-size: 14px;">Cher '.$user->name.' '.$user->last_name.',&nbsp;</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Nous avons re??u une notification indiquant que vous avez oubli?? votre mot de passe.&nbsp; Voici un lien que vous pouvez utiliser pour r??initialiser votre mot de passe : '.$link.'.&nbsp;</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Vous n???avez pas demand?? ?? r??initialiser votre mot de passe ? Vous avez d???autres questions ? Il suffit de r??pondre ?? ce courriel pour parler ?? l???un des membres de notre ??quipe.&nbsp;</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Cordialement,</span></font></p><p><font color="#999999"><span style="font-size: 14px;">L?????quipe du GProCongr??s II</span></font></p><div><br></div>';
        
                }elseif($user->language == 'pt'){
                
                    $link = '<a href="'.url('reset-password/'.$to.'/'.$token).'">aqui</a>';

                    $subject = "Redefinir sua senha.";
                    $msg = '<p><font color="#999999"><span style="font-size: 14px;">Prezado '.$user->name.' '.$user->last_name.',</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Recebemos uma notifica????o de que voc?? se esqueceu da sua senha. Aqui est?? o link que pode usar para redefinir sua senha: '.$link.';</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">N??o pediu para redefinir sua senha? Tem qualquer outra pergunta? Simplesmente responda este email para falar com um dos membros da nossa equipe.</span></font></p><p><span style="font-size: 14px;"><br></span></p><p><font color="#999999"><span style="font-size: 14px;">Calorosamente,</span></font></p><p><font color="#999999"><span style="font-size: 14px;">Equipe do II CongressoGPro</span></font></p><div><br></div>';
                
                }else{
                
                    $link = '<a href="'.url('reset-password/'.$to.'/'.$token).'">Click here</a>';

                    $subject = 'Reset your password';
                    $msg = '<div><div><div><span style="font-size: 14px;">Dear '.$user->name.' '.$user->last_name.',</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">We received notification that you have forgotten your password.&nbsp; Here???s a link you can use to reset your password: '.$link.'.</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Didn???t ask to reset your password? Have any other questions? Simply reply to this email to speak with one of our team members.</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Warmly,</span></div><div><span style="font-size: 14px;">GProCongress II Team&nbsp;</span></div></div></div>';
                                    
                }
                \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

                \Mail::send('email_templates.forgot-password', compact('to', 'token', 'subject','name','msg'), function($message) use ($to, $subject) {
					$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
					$message->subject($subject);
					$message->to($to);
				});
                

                \App\Helpers\commonHelper::setLocale();
                return response(array('reset'=>true, 'message'=> \Lang::get('web/home.WeHave-sentPassword-resetLinkOn-yourEmail-address')), $result->status);
    
            }else{
                return response(array('message'=>$resultData['message']), $result->status);
            }

        }

    }

    public function resetPassword(Request $request, $email='', $token=''){

        if($request->ajax()){

            $data=array(
                'token'=>$request->post('token'),
                'email'=>$request->post('email'),
                'password'=>$request->post('password'),
                'password_confirmation'=>$request->post('password_confirmation'),
            );
    
            $result=\App\Helpers\commonHelper::callAPI('POST', '/reset-password?lang='.\Session::get('lang'), json_encode($data));
            $resultData=json_decode($result->content,true);
    
            if($result->status==200){
                
                \Session::put('reset_password', true);
                return response(array('reload'=>true, 'message'=>$resultData['message']), $result->status);
    
            }else{

                return response(array('message'=>$resultData['message']), $result->status);

            }

        }

        $tokenResult=\DB::table('password_resets')->where([
            ['email','=',$email],
            ['token','=',$token],
            ])->first();

        if(!$tokenResult){

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Resetpassword-linkhasbeen-expired');
            \Session::flash('gpro_error', $message);
            return redirect()->route('home');

        }else {

            \App\Helpers\commonHelper::setLocale();
            return view('reset-password', compact('email', 'token'));
        }

    }
	
}
