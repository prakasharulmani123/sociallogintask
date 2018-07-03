<?php
error_reporting(E_ALL);
require_once ('./include/class.database.php');
$dbobj = new database();

ob_start();
session_start();

require_once("./app/classes/Handling.class.php");
$base_url = "https://sociallogin.my/";
$Configuration = array(
    #Base url
    "base_url" => $base_url,
    #Microsoft details
    "microsoft_client_id" => "9a20a74b-eba3-481f-96af-1c49790f3e50",
    "microsoft_client_secret" => "tkumzuRAZ9252oTKPL4*{_!",
    "microsoft_redirect_uri" => $base_url."microsoft.php",
);

if ($_GET['code']) {
    $scope = "https://graph.microsoft.com/mail.read https://graph.microsoft.com/Contacts.Read https://graph.microsoft.com/User.Read ";
    $token = json_decode(Handling::curlHttpRequest("https://login.microsoftonline.com/common/oauth2/v2.0/token", "post", array(
                "client_id" => $Configuration['microsoft_client_id'],
                "scope" => $scope,
                "code" => $_GET['code'],
                "redirect_uri" => $Configuration['microsoft_redirect_uri'],
                "grant_type" => "authorization_code",
                "client_secret" => $Configuration['microsoft_client_secret'],
    )));
    if (isset($token->access_token)) {
        $user_data = array();
        $get_user_request = "https://graph.microsoft.com/v1.0/me/";
        $user_response = curl_file_get_contents($get_user_request, $token->access_token);
        $user_response = json_decode($user_response);
        
        $user_profile_img_url = 'https://suite.social/login/default.jpg';
        $imageData = curl_file_get_contents($user_profile_img_url, $token->access_token);        
        $imageBase64 = 'data: image/jpeg;base64,'.base64_encode($imageData);
                      
        $user_data['user']['id'] = $user_response->id;
        $user_data['user']['displayName'] = $user_response->displayName;
        $user_data['user']['gender'] = "";
        $user_data['user']['email'] = $user_response->userPrincipalName;
        $user_data['user']['image'] = $imageBase64;
        $contacts = array();
        $get_contact_url = "https://graph.microsoft.com/v1.0/me/contacts/";
        while (1) {
            $user_contacts = curl_file_get_contents($get_contact_url, $token->access_token);

            $user_contacts = array_values((array) json_decode($user_contacts));
            if (isset($user_contacts[1]) && gettype($user_contacts[1]) == "string") {
                if (empty($contacts)) {
                    $contacts = $user_contacts[2];
                } else {
                    $contacts = array_merge($contacts, $user_contacts[2]);
                }
            } else {
                if (isset($user_contacts[1]) && gettype($user_contacts[1]) == "array")
                    $contacts = array_merge($contacts, $user_contacts[1]);
                break;
            }
            $get_contact_url = $user_contacts[1];
        }

        $records = Handling::returnarray($contacts, '1');
        $user_data['user']['record_count'] = count($records);
        $user_data['records'] = $records;
        $values = array("data" => json_encode(array($user_response->id => $user_data)), "service_type" => 1);
        $dbobj->insert("user_data",$values);
        
        $_SESSION['dashboard_uid'] = $user_data['user']['id'];
        $_SESSION['name'] = $user_data['user']['displayName'] ;
        $_SESSION['image'] = "https://suite.social/login/default.jpg";
        ?>
         
        <script type="text/javascript">
            opener.location.href = '<?php echo $base_url;?>index.php?msg=success';
            close();
        </script>
 
        <?php

    }
}
exit;

function curl_file_get_contents($url, $accessToken, $type = 0) {
    $curl = curl_init();
    $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $accessToken;
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Prefer: odata.maxpagesize=1000';


    curl_setopt($curl, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); //The number of seconds to wait while trying to connect.
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
