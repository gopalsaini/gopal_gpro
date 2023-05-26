<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PreLoginController;
use App\Http\Controllers\API\PostLoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:api'
  ], function() {

    Route::get("user-profile","API\PostLoginController@userProfile");
    Route::post("change-password","API\PostLoginController@changePassword");
    Route::post("profile-update","API\PostLoginController@updateProfile");
    Route::post("offline-payment","API\PostLoginController@offlinePayment");
    Route::post("travel-info","API\PostLoginController@travelInfo");
    Route::get("travel-info-details","API\PostLoginController@travelInfoDetails");
    Route::post("travel-info-verify","API\PostLoginController@travelInfoVerify");
    Route::post("session-info","API\PostLoginController@sessionInfo");
    Route::get("session-info-verify","API\PostLoginController@sessionInfoVerify");
    Route::post("group-leader","API\PostLoginController@GroupLeader");
    Route::post("spouse-add","API\PostLoginController@spouseAdd");
    Route::post("stay-room","API\PostLoginController@stayRooms");
    Route::post("contact-details","API\PostLoginController@contactDetails");
    Route::post("ministry-details","API\PostLoginController@ministryDetails");
    Route::post("pastoral-leaders-detailupdate","API\PostLoginController@updatePastoralLeader");
    Route::post("sponsor-payments-submit","API\PostLoginController@sponsorPaymentsSubmit");
    Route::post("full-payment-offline-submit","API\PostLoginController@fullPaymentOfflineSubmit");
    Route::post("travel-info-remark","API\PostLoginController@travelInfoRemark");

    
    Route::get("get-contact-details","API\PostLoginController@getContactDetails");
    Route::get("get-ministry-details","API\PostLoginController@getMinistryDetails");
    Route::get("get-country-list","API\PreLoginController@getCountryList");

    Route::get("logout","API\PostLoginController@logout");
    Route::get("stage-zero","API\PostLoginController@stageZero");
    Route::get("stage-one","API\PostLoginController@stageOne");
    Route::get("stage-two","API\PostLoginController@stageTwo");
    Route::get("session-info-details","API\PostLoginController@sessionInfoDetails");
    Route::post("donate-payments-submit","API\PostLoginController@donatePaymentsSubmit");
    Route::get("qr-code-api","API\PostLoginController@QrCode");
    Route::post("payment-intent-key-generated","API\PostLoginController@onlinePaymentByMobile");
    Route::get("payment-details","API\PostLoginController@PaymentDetails");
    Route::get("get-userA-all-stage-profile-data","API\PostLoginController@getUserAllStageProfileData");
    Route::get("travel-visa-Letter-file","API\PostLoginController@travelInfoLetter");
    Route::get('notification-list', 'API\PostLoginController@NotificationList');
    Route::post('change-user-language', 'API\PostLoginController@changeUserLanguage');

        
    Route::post('sponsorship-passport-info', "API\PostLoginController@passportInfo");
    Route::get('sponsorship-passport-details', "API\PostLoginController@passportInfoDetails");
    Route::post('sponsorship-letter-approve', "API\PostLoginController@sponsorshipLetterApprove");

    Route::get('sponsorship-info-approve', "API\PostLoginController@PassportInfoApprove");
    Route::post('sponsorship-info-reject', "API\PostLoginController@PassportInfoReject");
    Route::post("invite-user","API\PostLoginController@InviteUser");
    Route::post("mobile-invite-user","API\PostLoginController@MobileInviteUser");
    Route::post("visa-is-not-granted","API\PostLoginController@visaIsNotGranted");


});


Route::post('login', 'API\PreLoginController@login');
Route::post('registration', 'API\PreLoginController@registration');
Route::post('send-otp', 'API\PreLoginController@sendOtp');
Route::post('validate-otp', 'API\PreLoginController@validateOtp');
Route::post('send-token', 'API\PreLoginController@validateToken');

Route::get('profile-update-reminder', 'API\PreLoginController@profileUpdateReminder');
Route::get('payment-reminder', 'API\PreLoginController@paymentReminder');
Route::post('get-testimonials', 'API\PreLoginController@TestimonialList');
Route::post('get-home-content', 'API\PreLoginController@HomeContent');
Route::get('get-sessions', 'API\PreLoginController@GetSessions');
Route::post('get-faqs', 'API\PreLoginController@GetFAQs');
Route::post('post-support', 'API\PreLoginController@help');
Route::post('get-information/{id}', 'API\PreLoginController@GetInformation');

Route::post("forgot-password","API\PreLoginController@forgotPassword");
Route::post("reset-password","API\PreLoginController@resetPassword");

Route::post("help","API\PreLoginController@help");
Route::any("crone-job-sales-status","API\PreLoginController@croneJobSalesStatus");
Route::post("webhook-response","API\PreLoginController@webhookResponse");
Route::get("send-travel-info-reminder","API\PreLoginController@sendTravelInfoReminder");
Route::get("travel-info-update-reminder","API\PreLoginController@TravelInfoUpdateReminder");
Route::get("send-session-info-reminder","API\PreLoginController@sendSessionInfoReminder");

Route::get("Early-Bird-1Jun2023","API\PreLoginController@EarlyBird1Jun2023");
Route::get("spouse-confirmation-both-member-reminder","API\PreLoginController@spouseConfirmationMemberReminder");
Route::get("spouse-confirmation-first-reminder","API\PreLoginController@spouseConfirmationFirstReminder");
Route::get("spouse-confirmation-2-reminder","API\PreLoginController@spouseConfirmation2Reminder");
Route::get("spouse-confirmation-3-reminder","API\PreLoginController@spouseConfirmation3Reminder");
Route::post("getAllLanguageFolderFile","API\PreLoginController@getAllLanguageFolderFile");

Route::any("paypal-webhook-response","API\PreLoginController@paypalWebhookResponse");
Route::any("user-email-update-data","API\PreLoginController@userEmailUpdateData");
// Route::any("get-approved-user-send-email","API\PreLoginController@getApprovedUserSendEmail");
Route::any("user-update-payment-country","API\PreLoginController@userUpdatePaymentCountry");
Route::any("send-early-bird-reminder-mail","API\PreLoginController@sendEarlyBirdReminderMail");

Route::any("spouse-reject-action-cron","API\PreLoginController@SpouseRejectActionCron");
Route::any("setdateandSpouseReminder","API\PreLoginController@setdateandSpouseReminder");

Route::any("send-early-bird-reminder-new-mail","API\PreLoginController@sendEarlyBirdReminderNewEmail");



// Route::any("appologies-letter","API\PreLoginController@AppologiesLetter");
// Route::any("payment-reminder-time-set","API\PreLoginController@paymentReminderTimeSet");
// Route::any("Send-Emails-To-Apps-Available","API\PreLoginController@SendEmailsToAppsAvailable");
Route::get("speaker-list","API\PreLoginController@speakerList");
Route::get("pre-recorded-video-list","API\PreLoginController@PreRecordedVideoList");
Route::get('get-popup-model', 'API\PostLoginController@popUpModelList');
Route::get('get-site-setting', 'API\PostLoginController@SiteSettingList');
Route::get('get-user-profile', 'API\PreLoginController@userProfile');
Route::get('get-total-member-for-community', 'API\PreLoginController@getTotalMemberForCommunity');
Route::get('sendcheckUserDetailsPendingAmount', 'API\PreLoginController@sendcheckUserDetailsPendingAmount');
Route::get('sendEmailsToPaymentCompleteDone', 'API\PreLoginController@SendEmailsToPaymentCompleteDone');



// Route::any("appologies-letter","API\PreLoginController@AppologiesLetter");

