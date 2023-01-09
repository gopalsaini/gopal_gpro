<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailChimpController extends Controller
{
    public function index(Request $request)
    {
        $listId = env('MAILCHIMP_LIST_ID');

        
        // $mailchimp = new \Mailchimp(env('MAILCHIMP_KEY'));
        
        // $mailchimp->lists->subscribe(
        //     env('MAILCHIMP_LIST_ID'),
        //     ['email' => 'gopalsaini459@gmail.com'],
        //     null,
        //     null,
        //     false
        // );

        // dd('subscri send successfully.');

        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey('MAILCHIMP_KEY');

        $response = $mailchimp->messages->sendTemplate([
            "template_name" => "template_name",
            "template_content" => [[]],
            "message" => [],
        ]);
        print_r($response);
    }
}