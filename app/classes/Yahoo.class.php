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
                        "code" => 'code',
                        "redirect_uri" => $Configuration["yahoo_callback_url"],
                        "grant_type" => "authorization_code")));
            if (isset($token->access_token)) {
                echo $token->access_token; exit;
                $user_data = array();
                $request_user_info = Handling::curlHttpRequest("https://www.googleapis.com/plus/v1/people/me?alt=json&access_token=" . $token->access_token);
                $request = json_decode($request_user_info);
                $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&max-results=10000&oauth_token=' . $token->access_token;
                $results = $this->curl_file_get_contents($url);
                $results = json_decode($results);
                $results_count = count($results->feed->entry);
                $user_data['user']['id'] = $request->id;
                $user_data['user']['displayName'] = $request->displayName;
                $user_data['user']['gender'] = $request->gender;
                $user_data['user']['email'] = $request->emails[0]->value;
                $user_data['user']['image'] = $request->image->url;
                $user_data['user']['record_count'] = $results_count;                
                $records=Handling::returnarray($results->feed->entry, 0);
                $user_data['records'] = $records;
                return json_encode(array("status" => "success", "data" => array($request->id => $user_data)));
            }
        }
        #Auth URL
        $scopes = urlencode('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/contacts.readonly https://www.google.com/m8/feeds/');
        $url = "https://api.login.yahoo.com/oauth2/request_auth?client_id=" . $Configuration["yahoo_consumer_key"] . "&response_type=code&redirect_uri=" . $Configuration["yahoo_callback_url"] . "&&language=en-us";
        return json_encode(array("status" => "url", "data" => array("url" => $url)));
    }

    public function curl_file_get_contents($url) {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

        curl_setopt($curl, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); //The number of seconds to wait while trying to connect.

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //To stop cURL from verifying the peer's certificate.
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $contents = curl_exec($curl);
        curl_close($curl);
        return $contents;
    }

}
