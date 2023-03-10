<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/ 
  
Route::get('/', 'HomeController@index')->name('home');
Route::get('registration', 'HomeController@Registration')->name('registration');
Route::get('login', 'HomeController@Registration')->name('login');
Route::post('language','HomeController@localization')->name('language');

Route::get('get-state', "HomeController@getState");
Route::get('map-country', "HomeController@mapCountry");
Route::get('get-city', "HomeController@getCity");

Route::get('pricing', "PricingController@index")->name('pricing');
Route::get('information/{slug}', "HomeController@information")->name('information');
Route::get('faq', "HomeController@faq")->name('faq');

Route::post('registration', "LoginController@registration")->name('registration');
Route::post('send-otp', "LoginController@sendOtp")->name('send.otp');
Route::post('validate-otp', "LoginController@validateOtp")->name('validate.otp');
Route::post('login', "LoginController@login")->name('login');

Route::match(['get','post'],'forgot-password', "LoginController@forgotPassword")->name('forgot.password');
Route::match(['get','post'],'reset-password/{email?}/{token?}', "LoginController@resetPassword")->name('reset.password');
Route::match(['get','post'],'sponsor-payment-link/{token?}', "HomeController@sponsorPaymentLink")->name('sponsor.payment.link');
Route::match(['get','post'],'sponsor-payments-pay', "HomeController@sponsorPaymentsPay")->name('sponsor-payments-pay');
Route::match(['get','post'],'donate-payments-submit', "HomeController@donatePaymentsSubmit")->name('donate-payments-submit');
Route::match(['get','post'],'stripe', 'HomeController@stripePost')->name('stripe.post');
Route::match(['get','post'],'stripe/{id}', 'HomeController@stripePaymentPage')->name('stripe.page');


Route::match(['get','post'],'spouse-confirm-registration/{token?}', "HomeController@SpouseConfirmRegistration")->name('spouse-confirm-registration');
Route::match(['get','post'],'spouse-confirm/{type?}/{token?}', "HomeController@SpouseConfirmAction")->name('spouse-confirm-action');
Route::match(['get','post'],'email-registration-confirm/{token?}', "LoginController@emailRegistrationConfirm")->name('email-registration-confirm');

Route::match(['get','post'], 'help', "HomeController@help")->name('help');

Route::group(['middleware'=>'Userauth'],function(){

	Route::match(['get','post'],'change-password', 'ProfileController@changePassword')->name('user.change-password'); 
	Route::get('logout', 'ProfileController@logOut');

	Route::group(['middleware'=>'UserCheckPassword'],function(){ 

		Route::get('profile', 'ProfileController@index')->name('profile'); 
		Route::get('payment', 'ProfileController@payment')->name('payment'); 
		Route::get('travel-information', 'ProfileController@travelInformation')->name('travel_info'); 
		Route::match(['get','post'],'groupinfo-update', 'ProfileController@groupInfo')->name('groupinfo-update'); 
		Route::match(['get','post'],'profile-update', 'ProfileController@profileDetails')->name('profile-update');
		Route::post('spouse-update', 'ProfileController@spouseUpdate')->name('spouse-update');
		Route::post('room-update', 'ProfileController@roomUpdate')->name('room-update');
		Route::match(['get','post'],'spouseinfo-update', 'ProfileController@spouseInfoUpdate')->name('spouseinfo-update');
		Route::match(['get','post'],'contact-details', 'ProfileController@contactDetails')->name('contact-details');
		Route::match(['get','post'],'ministry-details', 'ProfileController@ministryDetails')->name('ministry-details'); 
		Route::post('pastoral-leaders-detailupdate', 'ProfileController@updatePastoralLeader')->name('pastoral-leaders-detailupdate'); 
		Route::post('sponsor-payments-submit', 'ProfileController@sponsorPaymentsSubmit')->name('sponsor-payments-submit'); 
		Route::get('online-payment-full/{type}', 'ProfileController@OnlinePaymentFull')->name('online-payment-full'); 
		Route::post('full-payment-offline-submit', 'ProfileController@fullPaymentOfflineSubmit')->name('full-payment-offline-submit'); 
		Route::post('travel-information-submit', 'ProfileController@travelInformationSubmit')->name('travel-information-submit'); 
		Route::post('travel-information-remark-submit', 'ProfileController@travelInformationRemarkSubmit')->name('travel-information-remark-submit'); 
		Route::get('group-information', 'ProfileController@getGroupInformation')->name('group-information'); 
		Route::get('travel-information-verify', 'ProfileController@travelInformationVerify')->name('travel-information-verify'); 
		Route::get('session-information', 'ProfileController@SessionInformation')->name('session-information'); 
		Route::get('event-day-information', 'ProfileController@EventDayInformation')->name('event-day-information'); 
		Route::post('session-information-submit', 'ProfileController@SessionInformationSubmit')->name('session-information-submit'); 
		Route::get('session-information-final-submit', 'ProfileController@SessionInformationFinalSubmit')->name('session-information-final-submit'); 

		Route::get('paypal-payment-success', 'HomeController@PaypalSuccessUrl')->name('paypal-payment-success'); 
		Route::get('paypal-payment-error', 'HomeController@PaypalErrorUrl')->name('paypal-payment-error'); 
		
		Route::match(['get','post'], 'passport-info', "ProfileController@sponsorshipPassportInfo")->name('passport.info');
		Route::match(['get','post'], 'sponsorship-letter-approve', "ProfileController@sponsorshipLetterApprove")->name('sponsorshipLetter');
		Route::get('sponsorship-confirm/confirm/{id}', "ProfileController@PassportInfoApprove")->name('sponsorshipLetterApprove');
		Route::post('sponsorship-confirm/decline/', "ProfileController@PassportInfoReject")->name('sponsorshipLetterReject');


	});
});

//Admin urls
Route::get('/admin',function(){
	return redirect('admin/login');
});

Route::get('admin/login', 'Auth\LoginController@showLoginForm');
Route::post('admin/login', 'Auth\LoginController@login')->name('admin.login'); 
	
Route::group(['middleware' => ['auth']], function() {
	Route::resource('roles', 'RoleController');
});

Route::group(['prefix'=>'admin','as'=>'admin','middleware'=>['auth','checkadminurl'],'as'=>'admin.'],function() {

	Route::match(['get','post'],'/change-password', 'Admin\AdminController@changePassword')->name('changepassword');
	Route::get('dashboard', 'Admin\DashboardController@index')->name('dashboard');
	Route::get('dashboard-2', 'Admin\Dashboard2Controller@index')->name('dashboard2');
	Route::post('language','Admin\DashboardController@localization')->name('language');
	Route::post('logout', 'Auth\LoginController@logout')->name('logout');

	Route::get('get-payments','Admin\DashboardController@getPayments');
	Route::get('get-users-by-country','Admin\DashboardController@getUserByCountry');
	Route::get('get-users-by-continents','Admin\DashboardController@getUserByContinents');
	Route::get('get-users-by-user-age','Admin\DashboardController@getUserByUserAge');
	Route::get('get-users-stage-ajax','Admin\DashboardController@getStages');
	Route::get('get-group-registered-chart-ajax','Admin\DashboardController@getGroupRegisteredChartAjax');
	Route::get('get-single-married-ws-chart-ajax','Admin\DashboardController@getSingleMarriedWSChartAjax');
	Route::get('get-married-ws-chart-ajax','Admin\DashboardController@getMarriedWSChartAjax');
	Route::get('get-pastoral-trainers-chart-ajax','Admin\DashboardController@getPastoralTrainersChartAjax');
	Route::get('get-payment-chart-ajax','Admin\DashboardController@getPaymentChartAjax');
	Route::get('get-payment-type-chart-ajax','Admin\DashboardController@getPaymentTypeChartAjax');
	Route::get('get-do-you-seek-pastoral-training-chart-ajax','Admin\DashboardController@getDoYouSeekPastoralTraining');
	
	Route::get('get-total-group-registration','Admin\DashboardController@TotalGroupRegistration');
	Route::get('get-single-married-coming','Admin\DashboardController@TotalMarriedCouples');
	Route::get('get-total-married-couples','Admin\DashboardController@SingleMarriedComing');

	// Dashboard 2
	Route::get('get-payments-2','Admin\Dashboard2Controller@getPayments');
	Route::get('get-users-by-country-2','Admin\Dashboard2Controller@getUserByCountry');
	Route::get('get-users-by-continents-2','Admin\Dashboard2Controller@getUserByContinents');
	Route::get('get-users-by-user-age-2','Admin\Dashboard2Controller@getUserByUserAge');
	Route::get('get-group-registered-chart-ajax-2','Admin\Dashboard2Controller@getGroupRegisteredChartAjax');
	Route::get('get-single-married-ws-chart-ajax-2','Admin\Dashboard2Controller@getSingleMarriedWSChartAjax');
	Route::get('get-married-ws-chart-ajax-2','Admin\Dashboard2Controller@getMarriedWSChartAjax');
	Route::get('get-pastoral-trainers-chart-ajax-2','Admin\Dashboard2Controller@getPastoralTrainersChartAjax');
	Route::get('get-payment-chart-ajax-2','Admin\Dashboard2Controller@getPaymentChartAjax');
	Route::get('get-payment-type-chart-ajax-2','Admin\Dashboard2Controller@getPaymentTypeChartAjax');
	Route::get('get-do-you-seek-pastoral-training-chart-ajax-2','Admin\Dashboard2Controller@getDoYouSeekPastoralTraining');
	Route::get('get-total-group-registration-2','Admin\Dashboard2Controller@TotalGroupRegistration');
	Route::get('get-single-married-coming-2','Admin\Dashboard2Controller@TotalMarriedCouples');
	Route::get('get-total-married-couples-2','Admin\Dashboard2Controller@SingleMarriedComing');


	// Designation
	Route::group(['prefix'=>'designation', 'as'=>'designation.'], function() {
		Route::match(['get','post'], 'add', 'Admin\DesignationController@add')->name('add');
		Route::get('list', 'Admin\DesignationController@list')->name('list');
		Route::get('edit/{id}', 'Admin\DesignationController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\DesignationController@delete')->name('delete');
		Route::post('status', 'Admin\DesignationController@status')->name('status');
	});

	// User
	Route::group(['prefix'=>'user', 'as'=>'user.'], function() {
		Route::match(['get','post'], 'add', 'Admin\UserController@add')->name('add');
		Route::get('edit/{id}', 'Admin\UserController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\UserController@delete')->name('delete');
		Route::get('approve/{id}', 'Admin\UserController@ProfileApproved')->name('approve');
		Route::get('reject/{id}', 'Admin\UserController@profileReject')->name('reject');
		Route::post('status', 'Admin\UserController@status')->name('status');
		Route::post('send-profile-update-reminder', 'Admin\UserController@sendProfileUpdateReminder')->name('send.profile.update.reminder');
		Route::match(['get','post'], 'stage-setting', 'Admin\UserController@stageSetting')->name('stage.setting');
		Route::match(['get','post'], 'refund-amount', 'Admin\UserController@refundAmount')->name('refund.amount');
		Route::match(['get','post'], 'sponsored-refund-amount', 'Admin\UserController@sponsoredRefundAmount')->name('sponsored.refund.amount');
		Route::post('cash-payment-submit', 'Admin\UserController@cashPaymentSubmit')->name('cash-payment-submit'); 

		Route::post('profile-status', 'Admin\UserController@profileStatus')->name('profile.status');
		Route::match(['get', 'post'], 'comment-to-user', 'Admin\UserController@commentToUser')->name('comment.to.user');
		Route::match(['get'], 'userHistoryList', 'Admin\UserController@userHistoryList')->name('userHistoryList');
		Route::match(['get'], 'userMailTriggerList', 'Admin\UserController@userMailTriggerList')->name('userMailTriggerList');
		Route::match(['get','post'],'userMail-TriggerList-Model', 'Admin\UserController@userMailTriggerListModel')->name('userMailTriggerListModel');
		Route::match(['get'], 'spouse-pending', 'Admin\UserController@spousePending')->name('spouse.pending');
		Route::match(['get'], 'stage-all-download-excel-file', 'Admin\UserController@stageAllDownloadExcelFile')->name('stage-all-download-excel-file');
	
		Route::get('{designation}', 'Admin\UserController@list')->name('list');

		Route::match(['get', 'post'], 'recover/user', 'Admin\UserController@userRecover')->name('recover');
		Route::match(['get', 'post'], 'get-ministry-data/user', 'Admin\UserController@getMinistryData')->name('get-ministry-data');

		Route::group(['prefix'=>'{type}'], function() {
			Route::get('stage/all', 'Admin\UserController@stageAll')->name('list.stage.all');
			Route::get('stage/zero', 'Admin\UserController@stageZero')->name('list.stage.zero');
			Route::get('stage/one', 'Admin\UserController@stageOne')->name('list.stage.one');
			Route::get('stage/two', 'Admin\UserController@stageTwo')->name('list.stage.two');
			Route::get('stage/three', 'Admin\UserController@stageThree')->name('list.stage.three');
			Route::get('stage/four', 'Admin\UserController@stageFour')->name('list.stage.four');
			Route::get('stage/five', 'Admin\UserController@stageFive')->name('list.stage.five');
		});

		
		Route::get('transaction-data/download', 'Admin\UserController@TransationDataExport')->name('transaction-data-download');

		Route::get('user-profile/{id}', 'Admin\UserController@userProfile')->name('profile');		
		Route::get('archive-user/{id}', 'Admin\UserController@archiveUser')->name('archiveUserDelete');

		Route::get('payment-history/{id}', 'Admin\UserController@paymentHistory')->name('payment.history');
		Route::get('sponsored-payment-history/{id}', 'Admin\UserController@sponsoredPaymentHistory')->name('sponsored.Payment.History');
		Route::get('donate-payment-history/{id}', 'Admin\UserController@donatePaymentHistory')->name('donate.Payment.History');
		Route::post('send-payment-reminder', 'Admin\UserController@sendPaymentReminder')->name('send.payment.reminder');
		Route::get('travel-info/{id}', 'Admin\UserController@travelInfo')->name('travel.info');
		Route::get('session-info/{id}', 'Admin\UserController@sessionInfo')->name('session.info');
		Route::post('send-travel-info-reminder', 'Admin\UserController@sendTravelInfoReminder')->name('send.travel.info.reminder');
		Route::post('travel-info-status', 'Admin\UserController@travelInfoStatus')->name('travel.info.status');
		Route::post('send-session-info-reminder', 'Admin\UserController@sendSessionInfoReminder')->name('send.session.info.reminder');
		Route::post('session-info-status', 'Admin\UserController@sessionInfoStatus')->name('session.info.status');

		Route::get('user-details/{id}', 'Admin\UserController@userDetails')->name('details');

		Route::post('get-profile-base-price', 'Admin\UserController@getProfileBasePrice')->name('get-profile-base-price');
		Route::post('get-offer-price', 'Admin\UserController@getOfferPrice')->name('get-offer-price');

		Route::post('group/users/list', 'Admin\UserController@groupUsersList')->name('group.users.list');


		Route::post('upload-draft-information', 'Admin\UserController@uploadDraftInformation')->name('upload-draft-information');
		Route::post('upload-final-information', 'Admin\UserController@uploadFinalInformation')->name('upload-final-information');

	});

	// Offer
	Route::group(['prefix'=>'offer', 'as'=>'offer.'], function() {
		Route::match(['get','post'], 'add', 'Admin\OfferController@add')->name('add');
		Route::get('list', 'Admin\OfferController@list')->name('list');
		Route::get('edit/{id}', 'Admin\OfferController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\OfferController@delete')->name('delete');
		Route::post('status', 'Admin\OfferController@status')->name('status');
	});

	// Sub Offer
	Route::group(['prefix'=>'offer/{offer_id?}/sub-offer', 'as'=>'sub.offer.'], function() {
		Route::match(['get','post'], 'add', 'Admin\SubOfferController@add')->name('add');
		Route::get('list', 'Admin\SubOfferController@list')->name('list');
		Route::get('edit/{id}', 'Admin\SubOfferController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\SubOfferController@delete')->name('delete');
		Route::post('status', 'Admin\SubOfferController@status')->name('status');
	});

	// Transaction
	Route::group(['prefix'=>'transaction', 'as'=>'transaction.'], function() {
		Route::get('list', 'Admin\TransactionController@list')->name('list');
		Route::get('delete/{id}', 'Admin\TransactionController@delete')->name('delete');
		Route::post('status', 'Admin\TransactionController@status')->name('status');
	});

	// Testimonial
	Route::group(['prefix'=>'testimonial', 'as'=>'testimonial.'], function() {
		Route::match(['get','post'], 'add', 'Admin\TestimonialController@add')->name('add');
		Route::get('list', 'Admin\TestimonialController@list')->name('list');
		Route::get('edit/{id}', 'Admin\TestimonialController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\TestimonialController@delete')->name('delete');
		Route::post('status', 'Admin\TestimonialController@status')->name('status');
	});

	// Information
	Route::group(['prefix'=>'information', 'as'=>'information.'], function() {
		Route::match(['get','post'], 'add', 'Admin\InformationController@add')->name('add');
		Route::get('list', 'Admin\InformationController@list')->name('list');
		Route::get('edit/{id}', 'Admin\InformationController@edit')->name('edit');
		Route::get('view/{id}', 'Admin\InformationController@view')->name('view');
		Route::get('delete/{id}', 'Admin\InformationController@delete')->name('delete');
		Route::post('status', 'Admin\InformationController@status')->name('status');
	});

	// FAQ
	Route::group(['prefix'=>'faq', 'as'=>'faq.'], function() {
		Route::match(['get','post'], 'add', 'Admin\FaqController@add')->name('add');
		Route::get('list', 'Admin\FaqController@list')->name('list');
		Route::get('edit/{id}', 'Admin\FaqController@edit')->name('edit');
		Route::get('view/{id}', 'Admin\FaqController@view')->name('view');
		Route::get('delete/{id}', 'Admin\FaqController@delete')->name('delete');
		Route::post('status', 'Admin\FaqController@status')->name('status');
	});

	// helpList
	Route::group(['prefix'=>'help', 'as'=>'help.'], function() {
		Route::get('list', 'Admin\FaqController@helpList')->name('list');
	});

	// FAQ
	Route::group(['prefix'=>'category', 'as'=>'category.'], function() {
		Route::match(['get','post'], 'add', 'Admin\CategoryController@add')->name('add');
		Route::get('list', 'Admin\CategoryController@list')->name('list');
		Route::get('edit/{id}', 'Admin\CategoryController@edit')->name('edit');
		Route::get('view/{id}', 'Admin\CategoryController@view')->name('view');
		Route::get('delete/{id}', 'Admin\CategoryController@delete')->name('delete');
		Route::post('status', 'Admin\CategoryController@status')->name('status');
	});

	// FAQ
	Route::group(['prefix'=>'notification', 'as'=>'notification.'], function() {
		Route::match(['get','post'], 'add', 'Admin\NotificationController@add')->name('add');
		Route::get('list', 'Admin\NotificationController@list')->name('list');
		Route::get('view/{id}', 'Admin\NotificationController@view')->name('view');
		
	});

	// session
	Route::group(['prefix'=>'session', 'as'=>'session.'], function() {
		Route::match(['get','post'], 'add', 'Admin\SessionController@add')->name('add');
		Route::get('list', 'Admin\SessionController@list')->name('list');
		Route::get('edit/{id}', 'Admin\SessionController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\SessionController@delete')->name('delete');
		Route::post('status', 'Admin\SessionController@status')->name('status');
	});

	// subadmin
	Route::group(['prefix'=>'subadmin', 'as'=>'subadmin.'], function() {
		Route::match(['get','post'], 'add', 'Admin\SubAdminController@add')->name('add');
		Route::get('list', 'Admin\SubAdminController@list')->name('list');
		Route::get('edit/{id}', 'Admin\SubAdminController@edit')->name('edit');
		Route::get('delete/{id}', 'Admin\SubAdminController@delete')->name('delete');
		Route::post('status', 'Admin\SubAdminController@status')->name('status');
	});
	
}); 

Route::get('createsnapshot-cronjob','CronjobController@index');