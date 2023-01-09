<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	

    protected function authenticated($request,$user){

        
        if(\Auth::user()->user_type!='1' && \Auth::user()->user_type!='2'){
           
            Auth::logout();
            return redirect('admin/login')->withErrors(['error'=>"Access denied"]);

        }else if(\Auth::user()->status!=1){

            Auth::logout();
            return redirect('admin/login')->withErrors(['error'=>"User is deactivated. Please Contact your Administrator."]);

        }else{

            
            $user_menu=\App\Models\User_role::select('user_roles.*','menus.*','menus.id as menuid')->leftJoin('menus','user_roles.menu_id','=','menus.id')->where('user_roles.designation_id',\Auth::user()->designation_id)->where('menus.status','1')->orderBy('sort','ASC')->get()->toArray();

            $all_menu=\App\Models\Menu::get()->toArray();

            $restricted_link = array();
            foreach ($all_menu as $data1) {
                $duplicate = false;
                foreach ($user_menu as $data2) {
                    if ($data1['id'] === $data2['menuid']) {
                        $duplicate = true;
                    }
                }
                if ($duplicate === false) {
                    $restricted_link[] = $data1['link'];
                }
            }

            $exception_uris = $restricted_link;

            Session::put('fivefernsadminrexceptionurl',$exception_uris);
            Session::put('fivefernsadminmenu',$user_menu);

        }
        
    }

	public function logout()
    { 
        Auth::logout();
        Session::forget('fivefernsadminrexceptionurl');
        Session::forget('fivefernsadminmenu');
		Session::forget('digitaloceanToken');
        return redirect('/admin/login');
    }

}
