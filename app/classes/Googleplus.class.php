<?php

/**
 *          RAFAEL FERREIRA © 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** Google **
 * ------------------------------------------------------------------------
 */
require_once("Handling.class.php");

class Googleplus {

    public static function get_email() {
        global $Configuration;

        if (isset($_GET['code'])) {
            $token = json_decode(Handling::curlHttpRequest("https://accounts.google.com/o/oauth2/token", "post", array(
                        "code" => $_GET['code'],
                        "client_id" => $Configuration["googleplus_client_id"],
                        "client_secret" => $Configuration["googleplus_client_secret"],
                        "redirect_uri" => $Configuration["googleplus_redirect_uri"],
                        "grant_type" => "authorization_code")));
            if (isset($token->id_token)) {
                $request_user_info = Handling::curlHttpRequest("https://www.googleapis.com/plus/v1/people/me?alt=json&access_token=" . $token->access_token);
                $request = json_decode($request_user_info);

                $user_data['user']['id'] = $request->id;
                $user_data['user']['displayName'] = $request->displayName;
                $user_data['user']['gender'] = isset($request->gender)?$request->gender:"";
                $user_data['user']['birthday'] = isset($request->birthday)?$request->birthday:"";
                $user_data['user']['email'] = $request->emails[0]->value;
                $user_data['user']['image'] = $request->image->url;
                $user_data['user']['record_count'] = "";
                $user_data['records'] = "";
                //return json_encode(array("status" => "success", "data" => array($request->id => $user_data)));
                return json_encode(array("status" => "success", "data" => $user_data));
            }
        }

        #Auth URL
        $scopes = urlencode('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/contacts.readonly https://www.google.com/m8/feeds/');
        $url = "https://accounts.google.com/o/oauth2/auth?client_id=" . $Configuration["googleplus_client_id"] . "&response_type=code&scope=" . $scopes . "&redirect_uri=" . $Configuration["googleplus_redirect_uri"] . "&access_type=online&approval_prompt=force";

        return json_encode(array("status" => "url", "data" => array("url" => $url)));
    }

}
