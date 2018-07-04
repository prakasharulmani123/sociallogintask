<?php

/**
 *          RAFAEL FERREIRA © 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** Google **
 * ------------------------------------------------------------------------
 */
require_once("Handling.class.php");

class Yahoo {

    public function get_email() {
        global $Configuration;

        if (isset($_GET['code'])) {
            $token = json_decode(Handling::curlHttpRequest("https://api.login.yahoo.com/oauth2/get_token", "post", array(
                        "client_id" => $Configuration["yahoo_consumer_key"],
                        "client_secret" => $Configuration["yahoo_consumer_secret"],
                        "code" => $_GET['code'],
                        "redirect_uri" => $Configuration["yahoo_callback_url"],
                        "grant_type" => "authorization_code")));
            if (isset($token->access_token)) {
                $user_data = array();
                $request_user_info = Handling::curlHttpRequest("https://social.yahooapis.com/v1/user/me/profile?access_token=" . $token->access_token ."&format=json");
                $request = json_decode($request_user_info)->profile;

                $url = 'https://social.yahooapis.com/v1/user/me/contacts?access_token=' . $token->access_token .'&format=json';
                $results = Handling::curlHttpRequest($url);
                $results = json_decode($results)->contacts;

                $user_data['user']['id'] = $request->guid;
                $user_data['user']['displayName'] = $request->nickname;
                $user_data['user']['gender'] = ($request->gender == 'M') ? 'Male' : 'Female';
                $user_data['user']['email'] = @$request->emails[0]->handle;
                $user_data['user']['image'] = $request->image->imageUrl;
                $user_data['user']['record_count'] = $results->total;
                $records=Handling::returnarray($results->contact, 7);
                $user_data['user']['records'] = $records;

                return json_encode(array("status" => "success", 'guid' => $request->guid, "data" => array($request->guid => $user_data)));
            }
        }
        #Auth URL
        $url = "https://api.login.yahoo.com/oauth2/request_auth?client_id=" . $Configuration["yahoo_consumer_key"] . "&response_type=code&redirect_uri=" . $Configuration["yahoo_callback_url"] . "&&language=en-us";
        return json_encode(array("status" => "url", "data" => array("url" => $url)));
    }
}
