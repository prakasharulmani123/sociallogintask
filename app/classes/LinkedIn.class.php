<?php

require_once("Handling.class.php");
//include_once 'linkedin/http.php';
//include_once 'linkedin/oauth_client.php';

class LinkedIn {

    private $_config = array();
    private $_state = null;
    private $_access_token = null;
    private $_access_token_expires = null;
    private $_debug_info = null;
    private $_curl_handle = null;

    const API_BASE = 'https://api.linkedin.com/v1';
    const OAUTH_BASE = 'https://www.linkedin.com/uas/oauth2';
    const SCOPE_BASIC_PROFILE = 'r_basicprofile'; // Name, photo, headline, and current positions
    const SCOPE_FULL_PROFILE = 'r_fullprofile'; // Full profile including experience, education, skills, and recommendations
    const SCOPE_EMAIL_ADDRESS = 'r_emailaddress'; // The primary email address you use for your LinkedIn account
    const SCOPE_CONTACT_INFO = 'r_contactinfo'; // Address, phone number, and bound accounts
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';

    public function __construct(array $config) {
        if (!isset($config['api_key']) || empty($config['api_key'])) {
            throw new InvalidArgumentException('Invalid api key - make sure api_key is defined in the config array');
        }

        if (!isset($config['api_secret']) || empty($config['api_secret'])) {
            throw new InvalidArgumentException('Invalid api secret - make sure api_secret is defined in the config array');
        }

        if (!isset($config['callback_url']) || empty($config['callback_url'])) {
            throw new InvalidArgumentException('Invalid callback url - make sure callback_url is defined in the config array');
        }

        if (!extension_loaded('curl')) {
            throw new RuntimeException('PHP CURL extension does not seem to be loaded');
        }

        $this->_config = $config;
    }

    public static function get_email() {
        global $Configuration;
        if (isset($_GET['code'])) {
            $para = array(
                "grant_type" => "authorization_code",
                "code" => $_GET['code'],
                "client_id" => $Configuration['linkedin_api_key'],
                "client_secret" => $Configuration['linkedin_api_secret'],
                "redirect_uri" => $Configuration['linkedin_callback_url']
            );
            $token = Handling::curlHttpRequest("https://www.linkedin.com/oauth/v2/accessToken", "post", $para);
            $token = json_decode($token);
            $token = $token->access_token;
            if (!empty($token)) {
                $request = Handling::curlHttpRequest("https://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name,date-of-birth)?format=json&oauth2_access_token=" . $token);
                $resquest_response = json_encode(array("status" => "success", "data" => array("profile" => json_decode($request))));
                return $resquest_response;
            }
        }
        #Auth URL
        $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" . $Configuration['linkedin_api_key'] . "&redirect_uri=" . $Configuration['linkedin_callback_url'] . "&state=987654321&scope=r_basicprofile%20r_emailaddress";
        return json_encode(array("status" => "url", "data" => array("url" => $url)));
    }

}
