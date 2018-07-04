<?php
//error_reporting(0);

error_reporting(E_ALL);
require_once ('./include/class.database.php');
$dbobj = new database();
$response_data = array();

///////////////////////// LOGIN APPS /////////////////////////

ob_start();
session_start();

$base_url = "https://sociallogin.com/";

$Configuration = array(
    #Base url
    "base_url" => $base_url,
    #Yahoo details
    "yahoo_consumer_key" => 'dj0yJmk9OFZYanhPV2E4dElvJmQ9WVdrOU5uTlBjMHhvTjJjbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mMw--',
    "yahoo_consumer_secret" => '1498b56762ab8fe6f059fac25fe8b7ee78992cc0',
    "yahoo_callback_url" => $base_url . 'index.php?type=yahoo',
    #Twitter details
    "twitter_consumer_key" => '',
    "twitter_consumer_secret" => '',
    "twitter_callback_url" => $base_url . 'twitter.php',
    "twitter_follow_user_name" => "socialgrower",
    #Instagram details
    "instagram_app_key" => '',
    "instagram_app_secret" => '',
    "instagram_app_callback_url" => $base_url . 'index.php?type=instagram',
    "instagram_follow_user_id" => "",
    #Linkedin
    "linkedin_api_key" => "",
    "linkedin_api_secret" => "",
    "linkedin_callback_url" => $base_url . "index.php?type=linkedin",
    #Youtube details
    "google_youtube_client_id" => "",
    "google_youtube_client_secret" => "",
    "google_youtube_channel_id" => "", // Youtube for developer
    "google_youtube_redirect_uri" => $base_url . "index.php?type=youtube",
    #Facebook details
    "facebook_appid" => "",
    "facebook_appsecret" => "",
    "facebook_redirect_url_slug" => "",
    "facebook_redirect_url" => $base_url . "index.php?type=facebook",
    #Google plus details
    "googleplus_client_id" => "",
    "googleplus_client_secret" => "",
    "googleplus_redirect_uri" => $base_url . "index.php?type=googleplus",
    #Campaign Monitor details
    "campaignmonitor_client_id" => "113850",
    "campaignmonitor_client_secret" => "RuS4iXRyQ4k4ybGMFBsxIxChrJnf444ciYuZSeR4BIvu4q1PMwQWe4n4EJyti4IY4L44B4CY1y4RrN44",
    "campaignmonitor_redirect_uri" => $base_url . "index.php?type=campaignmonitor",
    #Get Response details
    "getresponse_client_id" => "",
    "getresponse_client_secret" => "",
    "getresponse_redirect_uri" => $base_url . "index.php?type=getresponse",
    #Constant Contact details
    "constantcontact_client_id" => "",
    "constantcontact_client_secret" => "",
    "constantcontact_redirect_uri" => $base_url . "index.php?type=constantcontact",
    #Mailchimp details
    "mailchimp_client_id" => "",
    "mailchimp_client_secret" => "",
    "mailchimp_oauth_domain" => "suite.social",
    "mailchimp_redirect_uri" => $base_url . "index.php?type=mailchimp",
    "mailchimp_access_token" => "",
    #Microsoft details
    "microsoft_client_id" => "9a20a74b-eba3-481f-96af-1c49790f3e50",
    "microsoft_client_secret" => "tkumzuRAZ9252oTKPL4*{_!",
    "microsoft_redirect_uri" => $base_url . "index/microsoft",
    #Google details
    "google_client_id" => "",
    "google_client_secret" => "",
    "google_redirect_uri" => $base_url . "index.php?type=google",
);
$ActiveServices = array(
    "facebook" => true, # set true to ativate facebook subscribers
    "linkedin" => true, # set true to ativate facebook subscribers
    "googleplus" => true, # set true to ativate googleplus subscribers
    "youtube" => true, # set true to ativate googleplus subscribers
    "mailchimp" => true, # set true to ativate facebook subscribers
    "google" => true, # set true to ativate google subscribers
    "constantcontact" => true, # set true to ativate google subscribers
    "campaignmonitor" => true, # set true to ativate the subscription via youtube	
    "email" => true, # set true to ativate the subscription via email
    "getresponse" => true, # set true to ativate the subscription via email
    "instagram" => true, # set true to ativate the subscription via email
    "twitter" => true, # set true to ativate the subscription via email
    "yahoo" => true, # set true to ativate the subscription via email
    "microsoft" => true, # set true to ativate the subscription via email
);
$responsePage = array("success" => "success",
    "error" => "error",
    "repeated" => "repeated",
    "bad_email" => "bad_email",
    "phone_verified" => "phone_verified",
);


if (isset($_GET['type'])) {
	
    // For Yahoo
    if ($_GET['type'] == 'yahoo') {
        if (!$ActiveServices["yahoo"]) {
            exit("Service not active!");
        }
        
        require_once("app/classes/Yahoo.class.php");
        $yahoo = new Yahoo();
        $response = json_decode($yahoo->get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $user_data = array();
            $user = $response->data->{$response->guid}->user;

            $user_data['user']['id'] = $user->id;
            $user_data['user']['displayName'] = $user->displayName;
            $user_data['user']['gender'] = $user->gender;
            $user_data['user']['birthday'] = $user->birthday;
            $user_data['user']['email'] = $user->email;
            $user_data['user']['image'] = $user->image;
            $user_data['user']['record_count'] = $user->record_count;
            $user_data['records'] = $user->records;

            //echo "<pre>"; print_r($response);  die;
            $values = array("data" => json_encode(array($user->id => $user_data)), "service_type" => 5);

            $dbobj->insert("user_data", $values);
            $_SESSION['dashboard_uid'] = $user->id;
            $_SESSION['name'] = $user->displayName;
            $_SESSION['image'] = $user->image;

//            $values = array("data" => json_encode($response->data), "service_type" => 1);
//            $dbobj->insert($values);
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php

        } else {
            $page = $responsePage['error'];
        }
    }

// For Twitter	
    if ($_GET['type'] == 'twitter') {
        if (!$ActiveServices["twitter"]) {
            exit("Service not active!");
        }
        header("Location: " . $base_url . "twitter.php");
    }
    // For Instagram
    if ($_GET['type'] == 'instagram') {
        if (!$ActiveServices["instagram"]) {
            exit("Service not active!");
        }
        include_once 'app/classes/instagram.class.php';

        $instagram_app_key = $Configuration['instagram_app_key'];
        $instagram_app_secret = $Configuration['instagram_app_secret'];
        $instagram_follow_user_id = $Configuration['instagram_follow_user_id'];
        $instagram_app_callback_url = $Configuration['instagram_app_callback_url'];
        $instagram = new Instagram(array(
            'apiKey' => $instagram_app_key,
            'apiSecret' => $instagram_app_secret,
            'apiCallback' => $instagram_app_callback_url
        ));
        $code = $_GET['code'];
        if (true === isset($code)) {
            $user_data = array();
            $data = $instagram->getOAuthToken($code);
            $user_data['user']['id'] = $data->user->id;
            $user_data['user']['displayName'] = $data->user->username;
            $user_data['user']['gender'] = "";
            $user_data['user']['email'] = "";
            $user_data['user']['image'] = $data->user->profile_picture;
            $user_data['user']['record_count'] = "";
            $user_data['records'] = "";
            $values = array("data" => json_encode(array($data->user->id => $user_data)), "service_type" => 5);
            $dbobj->insert($values);
            header("Location: index.php");
        } else {
            $loginUrl = $instagram->getLoginUrl();
            header("Location: " . $loginUrl);
        }
    }
    // For Linkedin
    if ($_GET['type'] == 'linkedin') {
        if (!$ActiveServices["linkedin"]) {
            exit("Service not active!");
        }
        require_once("app/classes/LinkedIn.class.php");
        $response = json_decode(LinkedIn::get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            //echo "<pre>"; print_r($response); 
            $user_data = array();
            $user_data['user']['id'] = $response->data->profile->id;
            $user_data['user']['displayName'] = $response->data->profile->formattedName;
            $user_data['user']['gender'] = $response->data->profile->gender;
            $user_data['user']['birthday'] = $response->data->profile->birthday;
            $user_data['user']['email'] = $response->data->profile->emailAddress;
            $user_data['user']['image'] = $response->data->profile->pictureUrl;
            $user_data['user']['record_count'] = "";
            $user_data['records'] = "";
            
            $values = array("data" => json_encode(array($response->data->profile->id => $user_data)), "service_type" => 5);
            
            $dbobj->insert("user_data", $values);
            $_SESSION['dashboard_uid'] = $response->data->profile->id;
            $_SESSION['name'] = $response->data->profile->formattedName;
            $_SESSION['image'] = $response->data->profile->pictureUrl;
            
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }
    // For YouTube		
    if ($_GET['type'] == 'youtube') {
        if (!$ActiveServices["youtube"]) {
            die("Service not active!");
        }
        
        require_once("app/classes/Youtube.class.php");
        
        $response = json_decode(Youtube::get_email());
        
        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            //echo  "<pre>"; print_r($response->data);  die;
            $user_data = array();
            $user_data['user']['id'] = '222'.$response->data->profile->id;
            $user_data['user']['displayName'] = $response->data->profile->name;
            $user_data['user']['gender'] = "";
            $user_data['user']['email'] = $response->data->profile->email;
            $user_data['user']['image'] = $response->data->profile->picture;
            $user_data['user']['record_count'] = "";
            $user_data['records'] = "";
            
            //echo "<pre>"; print_r($user_data);  die;
            $values = array("data" => json_encode(array('222'.$response->data->profile->id => $user_data)), "service_type" => 5);
            
            $dbobj->insert("user_data", $values);
            $_SESSION['dashboard_uid'] = $response->data->profile->id;
            $_SESSION['name'] = $response->data->profile->name;
            $_SESSION['image'] = $response->data->profile->picture;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }

    // For Google plus
    if ($_GET['type'] == 'googleplus') {
        if (!$ActiveServices["googleplus"]) {
            exit("Service not active!");
        }
        require_once("app/classes/Googleplus.class.php");
        $response = json_decode(Googleplus::get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            
            $user_data = array();
            $user_data['user']['id'] = $response->data->user->id;
            $user_data['user']['displayName'] = $response->data->user->displayName;
            $user_data['user']['gender'] = $response->data->user->gender;
            $user_data['user']['birthday'] = $response->data->user->birthday;
            $user_data['user']['email'] = $response->data->user->email;
            $user_data['user']['image'] = $response->data->user->image;
            $user_data['user']['record_count'] = "";
            $user_data['records'] = "";
            
            //echo "<pre>"; print_r($response);  die;
            $values = array("data" => json_encode(array($response->data->user->id => $user_data)), "service_type" => 5);
            
            $dbobj->insert("user_data", $values);
            $_SESSION['dashboard_uid'] = $response->data->user->id;
            $_SESSION['name'] = $response->data->user->displayName;
            $_SESSION['image'] = $response->data->user->image;
            
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
            
        } else {
            $page = $responsePage['error'];
        }
    }

// For Facebook
    if ($_GET['type'] == 'facebook') {
        if (!$ActiveServices["facebook"]) {
            exit("Service not active!");
        }
        require_once("app/classes/Facebook.class.php");
        $response = json_decode(Facebook::get_email());
        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $user_data = array();
            $user_data['user']['id'] = $response->data->user->id;
            $user_data['user']['displayName'] = $response->data->user->displayName;
            $user_data['user']['gender'] = $response->data->user->gender;
            $user_data['user']['birthday'] = $response->data->user->birthday;;
            $user_data['user']['email'] = $response->data->user->email;
            $user_data['user']['image'] = $response->data->user->image;
            $user_data['user']['record_count'] = "";
            $user_data['records'] = "";
            
            //echo "<pre>"; print_r($response);  die;
            $values = array("data" => json_encode(array('222'.$response->data->user->id => $user_data)), "service_type" => 5);
            
            $dbobj->insert("user_data", $values);
            $_SESSION['dashboard_uid'] = $response->data->user->id;
            $_SESSION['name'] = $response->data->user->displayName;
            $_SESSION['image'] = $response->data->user->image;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }
// campaignmonitor
    if ($_GET['type'] == 'campaignmonitor') {
        if (!$ActiveServices["campaignmonitor"]) {
            exit("Service ont active!");
        }

        require_once("app/classes/Campaignmonitor.class.php");

        $response = json_decode(Campaignmonitor::get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $jsondata=json_encode($response->data);
            $dataArr = (array) json_decode($jsondata,true);
            $user_data=array();
           
              $dashboard_uid="";
            $name="";
            $image="";
            foreach ($dataArr as $key => $value) {
                $dashboard_uid=$value['user']['id'];
                $name=$value['user']['displayName'];
                $image=$value['user']['image'];
                $user_data[$value['user']['id']]['user']['id'] = $value['user']['id'];
                $user_data[$value['user']['id']]['user']['displayName'] = $value['user']['displayName'];
                $user_data[$value['user']['id']]['user']['gender'] = $value['user']['gender'];
                $user_data[$value['user']['id']]['user']['email'] = $value['user']['email'];
                $user_data[$value['user']['id']]['user']['image'] = $value['user']['image'];
                $user_data[$value['user']['id']]['user']['record_count'] = $value['user']['record_count'];  
                $user_data[$value['user']['id']]['records']=$value['user']['records'];
                $user_data[$value['user']['id']]['list_info']=$value['user']['list_info'];
            }
            $values = array("data" => json_encode($user_data), "service_type" => 1);
    
            $dbobj->insert('user_data',$values);
            
            $_SESSION['dashboard_uid'] = $dashboard_uid;
            $_SESSION['name'] = $name;
            $_SESSION['image'] = $image;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }

// For Get Response
    if ($_GET['type'] == 'getresponse') {
        if (!$ActiveServices["google"]) {
            exit("Service ont active!");
        }
        require_once("app/classes/Getresponse.class.php");

        $response = json_decode(Getresponse::get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
           
            $jsondata=json_encode($response->data);
            $dataArr = (array) json_decode($jsondata,true);
            $user_data=array();
            $dashboard_uid="";
            $name="";
            $image="";
            foreach ($dataArr as $key => $value) {
                $dashboard_uid=$value['user']['id'];
                $name=$value['user']['displayName'];
                $image=$value['user']['image'];

                $user_data[$value['user']['id']]['user']['id'] = $value['user']['id'];
                $user_data[$value['user']['id']]['user']['displayName'] = $value['user']['displayName'];
                $user_data[$value['user']['id']]['user']['gender'] = $value['user']['gender'];
                $user_data[$value['user']['id']]['user']['email'] = $value['user']['email'];
                $user_data[$value['user']['id']]['user']['image'] = $value['user']['image'];
                $user_data[$value['user']['id']]['user']['record_count'] = $value['user']['record_count'];  
                $user_data[$value['user']['id']]['records']=$value['user']['records'];
                $user_data[$value['user']['id']]['list_info']=$value['user']['list_info'];
            }

            $values = array("data" => json_encode($user_data), "service_type" => 1);
            $dbobj->insert('user_data',$values);

            $_SESSION['dashboard_uid'] = $dashboard_uid;
            $_SESSION['name'] = $name;
            $_SESSION['image'] = $image;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }

// For Constantcontact
    if ($_GET['type'] == 'constantcontact') {

        if (!$ActiveServices["constantcontact"]) {
            exit("Service not active!");
        }
        if (!isset($_COOKIE['ctct'])) {

            setcookie("ctct", "0");
        } else {
            if ($_COOKIE['ctct'] >= 1) {
                unset($_COOKIE["ctct"]);
                /* Or */
                setcookie("ctct", "0", time() - 1);
                if (!isset($_COOKIE['ctct'])) {
                    header("Location: index.php?type=constantcontact");
                }
            }
        }
        // setcookie("ctct","1");
        // exit;
        require_once './constantcontact.php';
        require_once("app/classes/Constantcontact.class.php");
        $response = json_decode(Constantcontact::get_email());

        if (isset($response) && $response->status == "url") {
            setcookie("ctct", $_COOKIE['ctct'] + 1);
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $values = array("data" => json_encode($response->data), "service_type" => 1);
            $dbobj->insert('user_data',$values);
            $dataArr=json_encode($response->data);
            $dataArr=(array)json_decode($dataArr,true);			
			
            $dashboard_uid="";
            $name="";
            $image="";
            foreach ($dataArr as $key => $value) {
                $dashboard_uid=$value['user']['id'];
                $name=$value['user']['displayName'];
                $image=$value['user']['image'];
            }

            $_SESSION['dashboard_uid'] = $dashboard_uid;
            $_SESSION['name'] = $name;
            $_SESSION['image'] = $image;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php

        } else {
            $page = $responsePage['error'];
        }
        unset($_COOKIE["ctct"]);
        /* Or */
        setcookie("ctct", "0", time() - 1);
        exit;
    }

// For Mailchimp
    if ($_GET['type'] == 'mailchimp') {

        if (!$ActiveServices["mailchimp"]) {
            exit("Service not active!");
        }
        require_once("app/classes/Mailchimp.class.php");

        $response = json_decode(Mailchimp::get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $values = array("data" => json_encode($response->data), "service_type" => 1);
            $dbobj->insert($values);

            $dataArr=json_encode($response->data);
            $dataArr=(array)json_decode($dataArr,true);

            $dashboard_uid="";
            $name="";
            $image="";
            foreach ($dataArr as $key => $value) {
                $dashboard_uid=$value['user']['id'];
                $name=$value['user']['displayName'];
                $image=$value['user']['image'];
            }

            $_SESSION['dashboard_uid'] = $dashboard_uid;
            $_SESSION['name'] = $name;
            $_SESSION['image'] = $image;   

            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php            
        } else {
            $page = $responsePage['error'];
        }
    }

// For Microsoft
    if ($_GET['type'] == 'microsoft') {

        if (!$ActiveServices["microsoft"]) {
            exit("Service not active!");
        }
        require_once("app/classes/Microsoft.class.php");
        $microsoft = new Microsoft();
        $response = json_decode($microsoft->get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $user_data = array();
            $user = $response->data->{$response->guid}->user;

            $user_data['user']['id'] = $user->id;
            $user_data['user']['displayName'] = $user->displayName;
            $user_data['user']['gender'] = $user->gender;
            $user_data['user']['birthday'] = $user->birthday;
            $user_data['user']['email'] = $user->email;
            $user_data['user']['image'] = $user->image;
            $user_data['user']['record_count'] = $user->record_count;
            $user_data['records'] = $user->records;

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
        } else {
            $page = $responsePage['error'];
        }
    }

// For Gmail
    if ($_GET['type'] == 'google') {
        if (!$ActiveServices["google"]) {
            exit("Service not active!");
        }
        require_once("app/classes/Google.class.php");
        $google = new Google();
        $response = json_decode($google->get_email());

        if (isset($response->status) && $response->status == "url") {
            header("Location: " . $response->data->url);
        } else if (isset($response->status) && $response->status == "success") {
            $values = array("data" => json_encode($response->data), "service_type" => 1);
            $dbobj->insert("user_data",$values);           
            $dataArr=json_encode($response->data);
            $dataArr=(array)json_decode($dataArr,true);

            $dashboard_uid="";
            $name="";
            $image="";
            foreach ($dataArr as $key => $value) {
                $dashboard_uid=$value['user']['id'];
                $name=$value['user']['displayName'];
                $image=$value['user']['image'];
            }

            $_SESSION['dashboard_uid'] = $dashboard_uid;
            $_SESSION['name'] = $name;
            $_SESSION['image'] = $image;
            ?>
            <script type="text/javascript">
                opener.location.href = '<?php echo $base_url; ?>index.php?msg=success';
                close();
            </script>
            <?php
        } else {
            $page = $responsePage['error'];
        }
    }
}

// For WhatsApp
if (isset($_POST["code"])) {
    $_SESSION["code"] = $_POST["code"];
    $_SESSION["csrf_nonce"] = $_POST["csrf_nonce"];
    $ch = curl_init();
    // Set url elements
    $fb_app_id = '102018820150735';
    $ak_secret = '668b3f6885046eef0d3dfb2e42fbb6de';

    $token = "AA|$fb_app_id|$ak_secret";
    // Get access token
    $url = 'https://graph.accountkit.com/v1.1/access_token?grant_type=authorization_code&code=' . $_POST["code"] . '&access_token=' . $token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    $info = json_decode($result);
    // Get account information
    $url = 'https://graph.accountkit.com/v1.1/me/?access_token=' . $info->access_token;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    $final = json_decode($result);
	$user_ac_id = isset($final->id) ? $final->id : '';
    $phone = isset($final->phone->number) ? $final->phone->number : '';
    $page = $responsePage['phone_verified'];
}
//$page = $responsePage['phone_verified'];

if (isset($_POST['get_content'])) {
    
    $user_data = array();
    $user_data['user']['id'] = isset($_POST['user_ac_id']) ? $_POST['user_ac_id'] : rand(10,100);
    $user_data['user']['displayName'] = $_POST['firstname'].' '.$_POST['lastname'];
    $user_data['user']['gender'] = "";
    $user_data['user']['email'] = $_POST['phone'];
    $user_data['user']['image'] = '';
    $user_data['user']['record_count'] = "";
    $user_data['records'] = "";

    $values = array("data" => json_encode(array($user_data['user']['id'] => $user_data)), "service_type" => 5);
    $dbobj->insert("user_data", $values);
	
	$_SESSION['dashboard_uid'] = $user_data['user']['id'];
    $_SESSION['name'] = $user_data['user']['displayName'];
    $_SESSION['image'] = "https://suite.social/login/default.jpg";
	
	//die();
    header("Location: index.php?msg=success");

    $group_id = isset($_SESSION['group_id']) ? $_SESSION['group_id'] : '';
    $api_key = isset($_SESSION['api_key']) ? $_SESSION['api_key'] : '';

    /* ############# Call social sender API  ############# */
    $url = "//suite.social/sender/ssem_api/sync_contact";
    $fields = array(
        "api_key" => $api_key,
        "first_name" => $firstname,
        "last_name" => $lastname,
        "mobile" => $phone,
        "email" => "-",
        "contact_group_id" => $group_id,
        "date_birth" => ""
    );
    httpPost($url, $fields);
    $page = $responsePage['success'];
}

function httpPost($url, $params) {
    $url = "http://suite.social/sender/ssem_api/sync_contact";
    $postData = '';
    foreach ($params as $k => $v) {
        $postData .= $k . '=' . $v . '&';
    }
    $postData = rtrim($postData, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $output = curl_exec($ch);
    curl_close($ch);
}

if (!isset($page))
    $page = '';

	///////////////////////// SHARELOCK HEADER /////////////////////////
	
    require_once "sharelock.class.php";
	
    //define array for sharelock
    /*-----------------------------------Array details-----------------------------------*/
	
    # "id"=>"1" - sets the unique sharelock id - change the id for new sharing pages with different share count.
    # "visitor_target"=>"5" - sets total no of targeted visitors - how many visitors are required to unlock your offer for each user.
    # "url"=>"https://YourWebiste.com/Download.zip" - sets download url after total visitor count.
	# "ip"=>"1" - Check ip detection set to 1 (for yes) or 0 (for no)
	# "reset"=>"1" - Resets the counter after user reaches visitor target, set to 1 (for yes) or 0 (for no)
	
    /*-----------------------------------Array details end-----------------------------------*/
		
    $data=array(
    '0'=>array("id"=>"1","visitor_target"=>"10","url"=>"https://suite.social/training","theme"=>"","ip"=>"1","reset"=>"1"),
    );
    $sharelock = new sharelock();
	
    //current url of file
    $uri = $_SERVER['REQUEST_URI'];
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];		
	
	///////////////////////// SHARELOCK SETTINGS /////////////////////////
	
	///////////////////////// SESSIONS ///////////////////////// 	
		
    $headline = 'Social Media Management, Marketing, Monitoring & Messaging for your business';
    //$caption = 'Your Caption Here';
	
	$_SESSION['headline'] = $headline;
	//$_SESSION['caption'] = $caption;
	
	///////////////////////// SESSIONS ///////////////////////// 	

?>
<!DOCTYPE html>
<html lang="en">
    <head>

     <!-- Title -->
     <title>Social Suite - Login</title>
     <!-- Meta Data -->
    <meta name="title" content="All-in-one Social Media Platform for businesses">
    <meta name="description" content="Social Media Management, Marketing, Monitoring & Messaging -  Save time, money and resources and GROW traffic, customers & sales 24-7, 365 days a year!">
    <meta name="keywords" content="Blog Management, Blog Marketing, Facebook Management, Facebook Marketing, Flickr Management, Flickr Marketing, Google+ Management, Google+ Marketing, Instagram Management, Instagram Marketing, Linkedin Management, Linkedin Marketing, Periscope Management, Periscope Marketing, Pinterest Management, Pinterest Marketing, Reddit Management, Reddit Marketing, Snapchat Management, Snapchat Marketing, Social Media Automation, Social Media Bot, Social Media Dashboard, Social Media Groups, Social Media Hub, Social Media Management, Social Media Manager, Social Media Marketer, Social Media Marketing, Social Media Monitoring, Social Media Poster, Social Media Promotion, Social Media Publisher, Social Media Publishing, Social Media Reports, Social Media Scheduler, Social Media Stream, Social Media Training, Social Media Wall, Soundcloud Management, Soundcloud Marketing, StumbleUpon Management, StumbleUpon Marketing, Tumblr Management, Tumblr Marketing, Twitter Management, Twitter Marketing, Vimeo Management, Vimeo Marketing, Vk Management, Vk Marketing, WhatsApp Management, WhatsApp Marketing, Wordpress Management, Wordpress Marketing, XING Management, XING Marketing, YouTube Management, YouTube Marketing">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <meta name="revisit-after" content="14 days">
    <meta name="author" content="Suite.social">	
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />		

	<!-- Google Plus -->
	<!-- Update your html tag to include the itemscope and itemtype attributes. -->
	<!-- html itemscope itemtype="//schema.org/{CONTENT_TYPE}" -->
	<meta itemprop="name" content="All-in-one Social Media Platform for businesses">
	<meta itemprop="description" content="Social Media Management, Marketing, Monitoring & Messaging -  Save time, money and resources and GROW traffic, customers & sales 24-7, 365 days a year!">
	<meta itemprop="image" content="//suite.social/images/thumb/suite.jpg">

	<!-- Twitter -->
	<meta name="twitter:card" content="All-in-one Social Media Platform for businesses">
	<meta name="twitter:site" content="@socialgrower">
	<meta name="twitter:title" content="All-in-one Social Media Platform for businesses">
	<meta name="twitter:description" content="Social Media Management, Marketing, Monitoring & Messaging -  Save time, money and resources and GROW traffic, customers & sales 24-7, 365 days a year!">
	<meta name="twitter:creator" content="@socialgrower">
	<meta name="twitter:image:src" content="//suite.social/images/thumb/suite.jpg">
	<meta name="twitter:player" content="">

	<!-- Open Graph General (Facebook & Pinterest) -->
	<meta property="og:url" content="//suite.social">
	<meta property="og:title" content="All-in-one Social Media Platform for businesses">
	<meta property="og:description" content="Social Media Management, Marketing, Monitoring & Messaging -  Save time, money and resources and GROW traffic, customers & sales 24-7, 365 days a year!">
	<meta property="og:site_name" content="Social Suite">
	<meta property="og:image" content="//suite.social/images/thumb/suite.jpg">
	<meta property="fb:admins" content="126878864054794">
	<meta property="fb:app_id" content="1382960475264672">
	<meta property="og:type" content="product">
	<meta property="og:locale" content="en_UK">

	<!-- Open Graph Article (Facebook & Pinterest) -->
	<meta property="article:author" content="126878864054794">
	<meta property="article:section" content="Marketing">
	<meta property="article:tag" content="Marketing">	

    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />	
	<meta name="HandheldFriendly" content="true" />	

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="//suite.social/images/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="72x72" href="//suite.social/images/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="//suite.social/images/favicon/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="144x144" href="//suite.social/images/favicon/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="256x256" href="//suite.social/images/favicon/apple-touch-icon-256x256.png" />
	
	<!-- Chrome for Android web app tags -->
	<meta name="mobile-web-app-capable" content="yes" />
	<link rel="shortcut icon" sizes="256x256" href="//suite.social/images/favicon/apple-touch-icon-256x256.png" />	

    <!-- CSS --> 
	<link rel="stylesheet" href="//suite.social/src/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="//suite.social/src/css/main.css">	
	<link rel="stylesheet" href="//suite.social/assets/css/social-buttons.css">
	
	<!-- Font Awesome -->
    <link rel="stylesheet" href="//suite.social/src/bower_components/font-awesome/css/font-awesome.min.css">
	
	<!-- Theme style -->
	<link rel="stylesheet" href="//suite.social/src/dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="//suite.social/src/dist/css/skins/skin-green.min.css">
	
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<!-- Google Font -->
	<!--<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
	<link href="//fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">

	<!-- Scripts -->
	<script src="https://sdk.accountkit.com/en_EN/sdk.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
	<script src="//apis.google.com/js/platform.js" async defer></script>		

<style>

/**************************************** BODY ****************************************/

body {
    overflow-x: hidden;
	color: #444;
	padding-right:0px !important;
	margin-right:0px !important;
}

a {
    color: #609450;
}

a:visited {
  color: #eee;
}

a:hover {
  color: #8ec657;
}

p {
    margin: 10px 0 10px;
}

.h1, .h2, .h3, h1, h2, h3, h4 {
    margin-top: 15px;
	margin-bottom:15px;
}

img {
    border-radius: 5px;
}

.box-title a:link { color: #609450; }

hr {
    border-top: 1px solid #ddd;
}

.navbar-brand {
    padding: 7px 15px;
}

.content-header {
    padding: 5px 15px 0 15px;
}

.fb_reset {
    display:none;
}

.main-footer {
    display: none;
}

.list-group {
    font-size: 20px;
}

/**************************************** MODAL ****************************************/

.modal-body {
overflow-x: hidden;
}

.modal.in .modal-dialog {
    width: 95%;
}

.modal {
 overflow-y: auto;
 background: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: transparent;
	-webkit-box-shadow: none;
	box-shadow: none;
}

.close {
    color: #fff;
    filter: alpha(opacity=90);
    opacity: .9;
}

/**************************************** ADMIN UI ****************************************/

.skin-green .main-header li.user-header {
    background-color: #404040;
}

.skin-green .main-header .navbar {
    background-color: #404040;
}

.skin-green .main-header .logo {
    background-color: #404040;
}

.skin-green .wrapper, .skin-green .main-sidebar, .skin-green .left-side {
    background-color: #404040;
}

.skin-green .sidebar-menu>li.header {
    color: #999;
    background: #262626;
}

.skin-green .sidebar-menu>li.active>a {
    background: #8ec657;
}

.skin-green .sidebar-menu>li:hover>a {
    background: #609450;
}

.skin-green .sidebar a {
    color: #ccc;
}

.skin-green .sidebar-menu>li.active>a {
    border-left-color: #609450;
}

.skin-green .sidebar-menu>li>.treeview-menu {
    margin: 0 1px;
    background: #262626;
}

.skin-green .sidebar-menu>li:hover>a, .skin-green .sidebar-menu>li.active>a, .skin-green .sidebar-menu>li.menu-open>a {
    color: #fff;
    background: #8ec657;
}

.skin-green .sidebar-menu .treeview-menu>li>a {
    color: #999;
}

.skin-green .main-header .logo:hover {
    background-color: #404040;
}

.skin-green .main-header .navbar .sidebar-toggle:hover {
    background-color: #609450;
}

.main-footer {
    background: #262626;
    color: #fff;
    border-top: 1px solid #262626;
}

.content-wrapper {
    background-color: #FAFAFA;
}

.content-header>.breadcrumb>li>a {
    color: #999;
}
		
.info-box-content {
    color: #333;
}	

.thumbnail {
    background-color: #404040;
    border: 1px solid #404040;
}

.box {
    background: #f5f5f5;
	border-radius: 5px;
}

.box.box-default {
    border-top-color: #8ec657;
}

.box-header.with-border {
    border-bottom: 1px solid #ddd;
}

.box.box-primary {
    border-top-color: #609450;
}

.small-box {
    /*border-radius: 5px;*/
	margin-bottom: 0px;
}

/**************************************** BUTTONS/BADGES ****************************************/

.btn-flex {
  display: flex;
  align-items: stretch;
  align-content: stretch;
}

.btn-flex .btn:first-child {
   flex-grow: 1;
   text-align: left;
}

.btn-app {
    border-radius: 3px;
    position: relative;
    padding: 5px 5px;
    margin: 0 0 10px 10px;
    min-width: 80px;
    height: 80px;
    width: 50%;
    text-align: center;
    color: #666;
    border: 1px solid #ddd;
    background-color: #f4f4f4;
    font-size: 18px;
}

.btn-success {
    background-color: #8ec657;
    border-color: #8ec657;
}

.btn-primary {
    background-color: #609450;
    border-color: #609450;
}

.btn-success:hover,
.btn-success:active,
.btn-success.hover {
  background-color: #609450;
  border-color: #609450;
}

.btn-primary:hover,
.btn-primary:active,
.btn-primary.hover {
  background-color: #8ec657;
  border-color: #8ec657;
}

.btn-success:focus,
.btn-success.focus {
  color: #fff;
    background-color: #609450;
    border-color: #609450;
}

.btn-primary:focus,
.btn-primary.focus {
  color: #fff;
    background-color: #8ec657;
    border-color: #8ec657;
}

.btn-success:active,
.btn-success.active,
.open > .dropdown-toggle.btn-primary {
  color: #fff;
    background-color: #8ec657;
    border-color: #8ec657;
}

.btn-primary:active,
.btn-primary.active,
.open > .dropdown-toggle.btn-primary {
  color: #fff;
    background-color: #609450;
    border-color: #609450;
}

.btn-success.active.focus, .btn-success.active:focus, .btn-success.active:hover, .btn-success:active.focus, .btn-success:active:focus, .btn-success:active:hover, .open>.dropdown-toggle.btn-success.focus, .open>.dropdown-toggle.btn-success:focus, .open>.dropdown-toggle.btn-success:hover {
    color: #fff;
    background-color: #8ec657;
    border-color: #8ec657;
}

.btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover {
    color: #fff;
    background-color: #609450;
    border-color: #609450;
}

.btn {
    border-radius: 5px;
}

.bg-success {
    background-color: #8ec657;
    color: #fff;
}

.bg-primary {
    color: #fff;
    background-color: #609450;
}

.bg-light-blue, .label-primary, .modal-primary .modal-body {
    background-color: #609450 !important;
}

.bg-green, .callout.callout-success, .alert-success, .label-success, .modal-success .modal-body {
    background-color: #8ec657 !important;
}

.bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
    background-color: #8ec657 !important;
}

.alert-info {
    border-color: #8ec657;
}

.alert-success {
    border-color: #8ec657;
}

.list-group-item {
    background-color: #f5f5f5;
    border: 1px solid #ddd;
}

label {
    color: #609450;
}

.badge {
    font-size: 20px;
}

.btn-reddit{color:#fff;background-color:#ff680a;border-color:rgba(0,0,0,0.2)}.btn-reddit:focus,.btn-reddit.focus{color:#fff;background-color:#ff4006;border-color:rgba(0,0,0,0.2)}
.btn-reddit:hover{color:#fff;background-color:#ff4006;border-color:rgba(0,0,0,0.2)}
.btn-reddit:active,.btn-reddit.active,.open>.dropdown-toggle.btn-reddit{color:#fff;background-color:#ff4006;border-color:rgba(0,0,0,0.2)}.btn-reddit:active:hover,.btn-reddit.active:hover,.open>.dropdown-toggle.btn-reddit:hover,.btn-reddit:active:focus,.btn-reddit.active:focus,.open>.dropdown-toggle.btn-reddit:focus,.btn-reddit:active.focus,.btn-reddit.active.focus,.open>.dropdown-toggle.btn-reddit.focus{color:#fff;background-color:#98ccff;border-color:rgba(0,0,0,0.2)}
.btn-reddit:active,.btn-reddit.active,.open>.dropdown-toggle.btn-reddit{background-image:none}
.btn-reddit.disabled:hover,.btn-reddit[disabled]:hover,fieldset[disabled] .btn-reddit:hover,.btn-reddit.disabled:focus,.btn-reddit[disabled]:focus,fieldset[disabled] .btn-reddit:focus,.btn-reddit.disabled.focus,.btn-reddit[disabled].focus,fieldset[disabled] .btn-reddit.focus{background-color:#ff680a;border-color:rgba(0,0,0,0.2)}
.btn-reddit .badge{color:#ff680a;background-color:#000}

</style>				

    </head>
<!-- ADD <p>The CLASS sidebar-collapse TO HIDE <p>The SIDEBAR PRIOR TO LOADING <p>The SITE -->
<body class="hold-transition skin-green layout-top-nav">
<!-- Site wrapper -->
<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="navbar-header">
          <a href="//www.ekaminfotech.com/login" class="navbar-brand"><img width="200px" src="//suite.social/images/logo/login.png"/></a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <!--<li><a href="#">Link</a></li>-->
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"><i class="fa fa-question-circle"></i> About</a></li>
				<li class="divider"></li>
                <li><a href="#"><i class="fa fa-usd"></i> Resellers</a></li>
				<li class="divider"></li>
                <li><a href="#"><i class="fa fa-rss"></i> Blog</a></li>
                <li class="divider"></li>
                <li><a href="#"><i class="fa fa-envelope"></i> Contact</a></li>
              </ul>
            </li>
          </ul>
          <!--<form class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
            </div>
          </form>-->
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
		  
            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-sign-in"></i>
              </a>
              <ul class="dropdown-menu">
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="//suite.social/login/" class="btn btn-default btn-lg btn-flat"><i class="fa fa-share-alt"></i> Login with your account!</a>

                </li>
              </ul>
            </li>

            <!-- Messages: style can be found in dropdown.less-->
            <li class="dropdown messages-menu">
              <!-- Menu toggle button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-envelope-o"></i>
              </a>
              <ul style="height: 250px;" class="dropdown-menu">
                <li class="header">Contact us</li>
                <li>
                  <!-- inner menu: contains the messages -->
                  <ul class="menu">
                    <li><!-- start message -->
                      <a href="tel:+447305800400">
                        <div class="pull-left">
                          <!-- User Image -->
                          <i style="color:#444" class="fa fa-phone"></i>
                        </div>
                        <!-- Message title and timestamp -->
                        <h4>
                          +44 (0) 730 5800 400
                          <small><i class="fa fa-clock-o"></i> 2 mins</small>
                        </h4>
                        <!-- The message -->
                        <p>Monday to Sunday, 9am - 9pm</p>
                      </a>
                    </li>
                    <li><!-- start message -->
                      <a href="https://m.me/www.suite.social" target="_blank">
                        <div class="pull-left">
                          <!-- User Image -->
                          <i style="color:#444" class="fa fa-comment"></i>
                        </div>
                        <!-- Message title and timestamp -->
                        <h4>
                          Facebook Messenger
                          <small><i class="fa fa-clock-o"></i> 15 mins</small>
                        </h4>
                        <!-- The message -->
                        <p>Send a message to our Facebook page</p>
                      </a>
                    </li>
                    <li><!-- start message -->
                      <a href="skype:socialgrower?chat">
                        <div class="pull-left">
                          <!-- User Image -->
                          <i style="color:#444" class="fa fa-skype"></i>
                        </div>
                        <!-- Message title and timestamp -->
                        <h4>
                          Skype
                          <small><i class="fa fa-clock-o"></i> 24-72 hours</small>
                        </h4>
                        <!-- The message -->
                        <p>Message us for live support</p>
                      </a>
                    </li>
                    <li><!-- start message -->
                      <a href="mailto:support@suite.social">
                        <div class="pull-left">
                          <!-- User Image -->
                          <i style="color:#444" class="fa fa-envelope"></i>
                        </div>
                        <!-- Message title and timestamp -->
                        <h4>
                          Email
                          <small><i class="fa fa-clock-o"></i> 1-3 days</small>
                        </h4>
                        <!-- The message -->
                        <p>Email us anytime</p>
                      </a>
                    </li>					
                  </ul>
                  <!-- /.menu -->
                </li>
              </ul>
            </li>
            <!-- /.messages-menu -->
			
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
    </nav>
  </header>

  <!-- =================CONTENT====================== -->
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!---------- Main content ---------->
    <section class="content">	 
  
        <div id="fb-root"></div>
        <script>(function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.8&appId=1382960475264672";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
			
<!------------------------SHARELOCK!------------------------>	

<?php
//print_r($data);
//echo $_SERVER['REMOTE_ADDR'];
//$string=isset($_GET['id']) ? $_GET['id'] : '';
$myip=$_SERVER['REMOTE_ADDR'];
$myip_add=str_replace(".", "", $myip);

    foreach($data as $key=>$value)
    { 
	$string=isset($_GET['id'.$value['id']]) ? $_GET['id'.$value['id']] : '';
    $total_visits=$sharelock->header($value['id'],$value['ip'],$string,$reset='0'); //retrieve value of counter
    $pending_counts=$value['visitor_target']-$total_visits; //retrieve value of visitor target
    $filenamev=$value['id'].'_'.$myip_add.'.txt';  //saves visitor IP address in txt file
    $fh = fopen($filenamev, 'w+');
    fwrite($fh, $total_visits); //checks if counter is less then target counter or not               
    if($value['visitor_target']>$total_visits) //list sharelock if counter is less than target counter
    { 
                				
    /*Shortcodes that list all the sharelock mentioned on the top of page in an array*/

    # echo $value['visitor_target']; - is the visitor target value
    # echo $total_visits; - is the number of visitors
    # echo $pending_counts; - is the total number of visitors              
    # echo $value['url']; - is the current url to share				
?>

                    <?php if (@$page == 'error') { ?>
					
<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-body">

                        <div class="col-md-6">		
                            <h3><b>Something went wrong! Sorry! Click back to try again.</b></h3>
                        </div>

                        <div class="col-md-6">						
							<p><a href="index.php" class="btn btn-primary btn-lg btn-block"><i class="fa fa-arrow-left"></i> GO BACK</a></p>							
                        </div>

                    </div>
                </div>
            </div>						
        </div>
					
                    <?php } elseif (@$page == 'repeated') { ?>

<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-body">

                        <div class="col-md-6">		
                            <h3><b>You have already subscribed!</b></h3>
                        </div>

                        <div class="col-md-6">						
							<p><a href="index.php" class="btn btn-primary btn-lg btn-block"><i class="fa fa-arrow-left"></i> GO BACK</a></p>							
                        </div>

                    </div>
                </div>
            </div>						
        </div>															
                        <?php
                    } elseif (@$page == 'success' || @$_GET['msg'] == 'success') {
        
                        /// SOCIAL SUITE LOGIN CHECK
                        if (!isset($_SESSION['dashboard_uid'])) {
                            session_destroy(); 
                            
                            header("Location: $base_url");
                        }                                                                
                        ?>                       
                        
<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
			<div class="box-header with-border">
              <i class="fa fa-check"></i>
              <h3 class="box-title">You have successfully logged in! Share with clients or colleagues to get free Social Media Training! <span class="text-muted">- Or proceed to Social Suite dashboard.</span></h3>
            </div>
            <!-- /.box-header -->				
                    <div class="box-body">

                        <div class="col-md-6">
							
<div class="box box-primary">
            <div class="box-body box-profile">
                <?php
                    $profile_pic = '//suite.social/src/dist/img/avatar04.png';
                    if(isset($_SESSION['image']) && !empty($_SESSION['image'])){
                        $profile_pic = $_SESSION['image'];
                    }
        
                ?>
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $profile_pic; ?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php if(isset($_SESSION['name']) && !empty($_SESSION['name'])){ echo $_SESSION['name']; }else{ echo 'Your Name'; } ?></h3>

              <p class="text-muted text-center">Social Suite User</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Your visitors so far:</b> <span class="pull-right badge bg-green"><?php echo $total_visits;?></span>
                </li>
                <li class="list-group-item">
                  <b>You will need:</b> <span class="pull-right badge bg-green"><?php echo $pending_counts; ?></span>
                </li>
                <li class="list-group-item">
                  <b>More visitors out of:</b> <span class="pull-right badge bg-green"><?php echo $value['visitor_target']; ?></span>
                </li>
              </ul>
			  
			  <p align="center" class="text-muted"><i>You must accept cookies in your browser. Click button and scroll down.</i></p>
		<?php if($string == ''){ ?>
	
		<?php }else{ 
		$param='?';		
		$pos = strpos($current_url, $param);
		$endpoint = $pos + strlen($param);
		$newStr = substr($current_url,0,$endpoint );
		?>
		<p><input class="form-control" type="text" value="<?php echo $newStr.'id'.$value['id'].'='.$myip_add; ?>" /></p>	
	<?php } ?>			  

			  <p><a href="#training" data-toggle="collapse" class="btn btn-success btn-lg btn-block">...TO GET FREE TRAINING! <i class="fa fa-arrow-right"></i></a></p>			  
			  <p><a href="#" data-toggle="modal" data-target="#share" class="btn btn-primary btn-lg btn-block">SHARE NOW <i class="fa fa-arrow-right"></i></a></p>

            </div>
            <!-- /.box-body -->
          </div>							
														
							<!--<p><div class="fb-like" data-href="https://www.facebook.com/<?php echo $fbpage; ?>" data-colorscheme="dark" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>&nbsp;&nbsp;<div class="g-follow" data-annotation="bubble" data-height="24" data-href="<?php echo $gppage; ?>" data-rel="publisher"></div></p><br>-->
							
                        </div>

                        <div class="col-md-6">		
						<img width="100%" src="//suite.social/images/screen/dashboard.jpg" alt="Dashboard">
						<p><a href="//suite.social/dashboard.php" class="btn btn-primary btn-lg btn-block">OR GO TO DASHBOARD <i class="fa fa-arrow-right"></i></a></p>							
                        </div>

                    </div>
                </div>
				
<div id="training" class="collapse">
			  
<!------------------------------ /TRAINING ------------------------------>				  

          <div class="box box-default">
            <div class="box-header">
              <i class="fa fa-warning"></i>
              <h3 class="box-title">Choose a social network to get free social training to from experts so you can outsmart competitors and grow profits.</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">	  
        
        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Blogger.jpg" alt="Blogger"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Blogger</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Publish your passions your way. Whether you'd like to share your knowledge, experiences or the latest news, create a unique and beautiful blog for free.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  

        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Delicious.jpg" alt="Delicious"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Delicious</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Delicious (stylized del.icio.us) is a social bookmarking web service for storing, sharing, and discovering web bookmarks.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/DeviantArt.jpg" alt="DeviantArt"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">DeviantArt</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>DeviantArt is an online artwork, videography and photography community. </p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>	
		  
        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Diigo.jpg" alt="Diigo"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Diigo</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Diigo is a powerful research tool and a knowledge-sharing community.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Facebook.jpg" alt="Facebook"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Facebook</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Create an account or log in to Facebook. Connect with friends, family and other people you know. Share photos and videos, send messages and get updates.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  

        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Flickr.jpg" alt="Flickr"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Flickr</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Flickr is an image hosting service and video hosting service. In addition to being a popular Web site for users to share and embed personal photographs and an online community, the service is widely used by photo researchers and by bloggers to host images that they embed in blogs and social media.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  
		  
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Forums.jpg" alt="Forums"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Forums</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Search marketing forums such as Warrior Forum, Digital Point, SEO Chat and more.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  
        
        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Flipboard.jpg" alt="Flipboard"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Flipboard</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Flipboard curates the world's stories so you can focus on investing in yourself, staying informed, and getting involved. With curated packages that offer insights and inspiration for any interest.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>	
		  
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Google-plus.jpg" alt="Google+"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Google+</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Discover amazing things and connect with passionate people.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Google-groups.jpg" alt="Google-groups"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Google+ Groups</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Google Groups is a service from Google that provides discussion groups for people sharing common interests. The Groups service also provides a gateway to Usenet newsgroups via a shared user interface.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Instagram.jpg" alt="Instagram"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Instagram</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>A simple, fun & creative way to capture, edit & share photos, videos & messages with friends & family.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  
		  
        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Linkedin.jpg" alt="Linkedin"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Linkedin</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Manage your professional identity. Build and engage with your professional network. Access knowledge, insights and opportunities.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Livejournal.jpg" alt="LiveJournal"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">LiveJournal</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>LiveJournal is a Russian social networking service where users can keep a blog, journal or diary.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
        
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Meetup.jpg" alt="Meetup"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Meetup</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Find Meetups so you can do more of what matters to you. Or create your own group and meet people near you who share your interests.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  

        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="//suite.social/marketer/periscope.php"><img width="100%" src="//suite.social/images/marketer/Periscope.jpg" alt="Periscope"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Periscope</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Periscope lets you broadcast and explore the world through live video. See where news is breaking, visit a new place, or meet people and share interests - all in real-time.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Pinterest.jpg" alt="Pinterest"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Pinterest</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Pinterest is a way to discover recipes, home ideas, style inspiration and other ideas to try.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>	  

        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Quora.jpg" alt="Quora"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Quora</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Quora is a place to gain and share knowledge. It's a platform to ask questions and connect with people who contribute unique insights and quality answers. This empowers people to learn from each other and to better understand the world.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  

        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Reddit.jpg" alt="Reddit"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Reddit</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Reddit gives you the best of the internet in one place. Get a constantly updating feed of breaking news, fun stories, pics, memes, and videos just for you. Passionate about something niche? Reddit has thousands of vibrant communities with people that share your interests.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Reviews.jpg" alt="Reviews"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Reviews</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Check your business reputation and rating by searching review sites such Yelp, FourSquare, Trip Advisor, Trust Pilot and more.</p>
	     <p><a style="color:#609450" href="//suite.social/marketer/reviews.php">COMMENT OR REPLY</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  

        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Slideshare.jpg" alt="Slideshare"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Slideshare</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Discover, Share, and Present presentations and infographics with the world's largest professional content sharing community.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Snapchat.jpg" alt="Snapchat"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Snapchat</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Snapchat lets you easily talk with friends, view Live Stories from around the world, and explore news in Discover. Life's more fun when you live in the moment!</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Soundcloud.jpg" alt="Soundcloud"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Soundcloud</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>SoundCloud is a music and podcast streaming platform that lets you listen to millions of songs from around the world, or upload your own.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
        
        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/StumbleUpon.jpg" alt="StumbleUpon"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Stumbleupon</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>StumbleUpon is the easiest way to discover new and interesting web pages, photos and videos across the Web.</p>
	     <a style="color:#609450" href="#">SHARE TO GET TRAINING</a>
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>		  		  
        
        <!-- Col 1 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Tumblr.jpg" alt="Tumblr"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group btn-flex">
        <button type="button" class="btn btn-primary">Tumblr</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Tumblr is a place to express yourself, discover yourself, and bond over the stuff you love. It's where your interests connect you with your people.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>

        <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Twitter.jpg" alt="Twitter"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">Twitter</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Twitter is an online news and social networking service on which users post and interact with messages known as "tweets".</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Vk.jpg" alt="Vk.com"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">Vk.com</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>VK is a Russian online social media and social networking service. It is available in several languages but it is especially popular among Russian-speaking users.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Xing.jpg" alt="Xing"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">Xing</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>XING is a European career-oriented social networking site for enabling a small-world network for professionals.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
		  
       <!-- Col 2 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Yahoo-Groups.jpg" alt="Yahoo-Groups"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">Yahoo Groups</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Yahoo! Groups is one of the world’s largest collections of online discussion boards.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div> 
		  
       <!-- Col 3 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Yahoo-Answers.jpg" alt="Yahoo-Answers"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">Yahoo Answers</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>In Yahoo Answers, you can ask about anything under the sun, including the sun, and get answers from real people.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div> 
		  
        <!-- Col 4 -->      
        <div class="col-md-3">
           <p><a href="#"><img width="100%" src="//suite.social/images/marketer/Youtube.jpg" alt="Youtube"></a></p>
		  
      <!-- Split button -->
      <div class="btn-group dropup btn-flex">
        <button type="button" class="btn btn-primary">YouTube</button>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div style="padding:12px" class="dropdown-menu pull-right">
        <p>Enjoy the videos and music you love, upload original content, and share it all with friends, family, and the world on YouTube.</p>
	     <p><a style="color:#609450" href="#">SHARE TO GET TRAINING</a></p>		 
        </div>
      </div>
      <!-- /Split button -->
		  
          </div>
        	
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

</div>	

<!------------------------------ /TRAINING ------------------------------>				
				
				
            </div>						
        </div>												
                    <?php } elseif (@$page == 'bad_email') { ?>
                        
<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-body">

                        <div class="col-md-6">		
                            <h3><b>Your email is wrong! Click back to try again.</b></h3>
                        </div>

                        <div class="col-md-6">						
							<p><a href="index.php" class="btn btn-primary btn-lg btn-block"><i class="fa fa-arrow-left"></i> GO BACK</a></p>							
                        </div>

                    </div>
                </div>
            </div>						
        </div>
                   <?php } elseif (@$page == 'phone_verified') { ?>
                        <div class="action_block bad_email">

                                <form method="POST">
                                    <p><input type="hidden" name="phone" value="<?php echo (isset($phone) && $phone != '') ? $phone : ''; ?>" />
									<input type="hidden" name="user_ac_id" value="<?php echo (isset($user_ac_id) && $user_ac_id != '') ? $user_ac_id : ''; ?>" />
									</p>
									
									
                                    <p><input type="text" name="firstname" required="required" class="form-control input-lg" placeholder="Enter your full name..."/></p>
                                    <p><input type="submit" class="btn btn-primary btn-lg" name="get_content" value="Submit and Proceed" /></p>
                                </form>

                        </div>
						
                    <?php } else { ?>
					
<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
			<div class="box-header with-border text-center">
              <i class="fa fa-warning"></i>
              <h3 class="box-title">Login with a provider to access Social Suite <span class="text-muted">- Please accept all app permissions to use all features.</span></h3>
            </div>
            <!-- /.box-header -->
                    <div class="box-body">					
					
<!-- Begin Login -->

        <div class="form-group">
							
<!-- Row 1 -->

        <!-- Col 1 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=facebook')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_facebook.jpg" alt="Facebook"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Facebook</button></p>
          </div></a>

        <!-- Col 2 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=googleplus')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_google-plus.jpg" alt="Google Plus"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Google Plus</button></p>
          </div></a>
		  
        <!-- Col 3 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=linkedin')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_linkedin.jpg" alt="Linkedin"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Linkedin</button></p>
          </div></a>
		  
        <!-- Col 4 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=twitter')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_twitter.jpg" alt="Twitter"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Twitter</button></p>
          </div></a>	  	

<!-- Row 2 -->
		  
        <!-- Col 1 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=youtube')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_youtube.jpg" alt="YouTube"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with YouTube</button></p>
          </div></a>

        <!-- Col 2 -->  
        <a style="cursor:pointer;" onClick="phone_btn_onclick();"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_whatsapp.jpg" alt="WhatsApp"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with WhatsApp</button></p>
          </a>
          <form action="" method="POST" id="my_form"><input type="hidden" name="code" id="code"><input type="hidden" name="csrf_nonce" id="csrf_nonce"></form>		  
		  </div>
		  
        <!-- Col 3 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=google')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_gmail.jpg" alt="Gmail"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Gmail</button></p>
          </div></a>
		  
        <!-- Col 4 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=microsoft')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_outlook.jpg" alt="Outlook"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Outlook</button></p>
          </div></a>	

<!-- Row 3 -->
		  
        <!-- Col 1 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=yahoo')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_yahoo.jpg" alt="Yahoo"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Yahoo</button></p>
          </div></a>

        <!-- Col 2 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=mailchimp')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_mailchimp.jpg" alt="Mailchimp"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Mailchimp</button></p>
          </div></a>
		  
        <!-- Col 3 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=constantcontact')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_constant-contact.jpg" alt="Constant Contact"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Constant Contact</button></p>
          </div></a>
		  
        <!-- Col 4 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=campaignmonitor')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_campaign-monitor.jpg" alt="Campaign Monitor"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Campaign Monitor</button></p>
          </div></a>

<!-- Row 4 -->
		  
        <!-- Col 1 -->  
        <a style="cursor:pointer;" onClick="popuplogin('index.php?type=getresponse')"><div class="col-md-3">
          <p><img width="100%" style="border:4px solid #ccc;border-radius:5px" src="//suite.social/images/logo/login_getresponse.jpg" alt="Get Response"></p>
		  <p><button class="btn-lg btn btn-success btn-block"><i class="fa fa-arrow-right"></i> Login with Get Response</button></p>
          </div></a>	  

<!-- End Login -->																		
                                </div>
								
                    </div>
                </div>
            </div>
						
        </div>								
								
                    <?php } ?>
		  
	  	  
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<!-- Share Modal -->
        <div class="modal fade" id="share">
          <div class="modal-dialog">
            <div class="modal-content">
              <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Choose network to share</h4>
              </div>-->
              <div align="center" class="modal-body">
		
	<h3 style="color:#fff">Use your custom share URL for Instagram, Messenger, Snapchat, WeChat & YouTube</h3>
      <div class="form-group">
        <input type="url" class="form-control input-lg" value="<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" placeholder="Share URL">
      </div>
	  
<div class="text-center">
      <h3 style="color:#fff">- OR -</h3>
    </div>	  
	  
<!--******************** SHARE BUTTONS ********************--->

      <div class="row">
        <div class="col-xs-4">
              <p><a href="https://www.facebook.com/sharer.php?u=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-facebook"><i class="fa fa-facebook fa-2x"></i> Facebook</a></p>
			  <p><a href="https://pinterest.com/pin/create/bookmarklet/?media=https://suite.social/images/thumb/suite.jpg&url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-pinterest"><i class="fa fa-pinterest fa-2x"></i> Pinterest</a></p>			 			  
              <p><a href="http://vk.com/share.php?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-instagram"><i class="fa fa-vk fa-2x"></i> VK</a></p>		
              <p><a href="https://www.blogger.com/blog-this.g?u=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&n=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-blogger"><i class="fa fa-rss fa-2x"></i> Blogger</a></p>
              <p><a href="http://www.livejournal.com/update.bml?subject=<?php echo $headline; ?>&event=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-livejournal"><i class="fa fa-pencil fa-2x"></i> LiveJournal</a></p>	
              <p><a href="https://mail.google.com/mail/?view=cm&fs=1&to&su=Recommendation&body=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>+&ui=2&tf=1&shva=1" target="_blank" class="btn btn-block btn-lg btn-social btn-google"><i class="fa fa-envelope fa-2x"></i> Gmail</a></p>			  
        </div>		

        <div class="col-xs-4">
              <p><a href="https://plus.google.com/share?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-google"><i class="fa fa-google-plus fa-2x"></i> Google+</a></p>		
			  <p><a href="https://www.linkedin.com/shareArticle?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&title=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-linkedin"><i class="fa fa-linkedin fa-2x"></i> Linkedin</a></p>		
              <p><a href="http://www.stumbleupon.com/submit?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&title=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-stumbleupon"><i class="fa fa-stumbleupon fa-2x"></i> Stumbleupon</a></p>	
              <p><a href="https://www.xing.com/app/user?op=share&url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-xing"><i class="fa fa-xing fa-2x"></i> Xing</a></p>
              <p><a href="whatsapp://send?text=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-whatsapp"><i class="fa fa-whatsapp fa-2x"></i> WhatsApp</a></p>	
              <p><a href="https://web.skype.com/share?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-skype"><i class="fa fa-skype fa-2x"></i> Skype</a></p>				  
        </div>		
		
        <div class="col-xs-4">	
              <p><a href="https://twitter.com/intent/tweet?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&text=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-twitter"><i class="fa fa-twitter fa-2x"></i> Twitter</a></p>		  
              <p><a href="https://reddit.com/submit?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&title=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-reddit"><i class="fa fa-reddit fa-2x"></i> Reddit</a></p>				  
			  <p><a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&title=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-tumblr"><i class="fa fa-tumblr fa-2x"></i> Tumblr</a></p>
              <p><a href="https://share.flipboard.com/bookmarklet/popout?v=2&title=<?php echo $headline; ?>&url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-flipboard"><i class="fa fa-clipboard fa-2x"></i> Flipboard</a></p>			  
              <!--<p><a href="#" target="_blank" class="btn btn-block btn-social btn-digg"><i class="fa fa-digg fa-2x"></i> Digg</a></p>-->			  
              <p><a href="https://telegram.me/share/url?url=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>&text=<?php echo $headline; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-telegram"><i class="fa fa-telegram fa-2x"></i> Telegram</a></p>		
              <p><a href="http://compose.mail.yahoo.com/?body=<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-lg btn-social btn-yahoo"><i class="fa fa-yahoo fa-2x"></i> Yahoo Mail</a></p>		
			  
        </div>		
		
      </div>
					  
              </div>
              <!--<div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>-->
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
		
<!------------------------EDIT LOCKED CONTENT------------------------>	
	  
<?php
                   
              }else
              { 
                //redirect to target url if counter is greater than target counter
              ?>
	  			  
<div class="row">
            <div class="col-md-12">
                <div class="box box-default">
			<div class="box-header with-border">
              <i class="fa fa-check"></i>
              <h3 class="box-title"><b>Congratulations!</b> You've reached targeted visitor share count! <span class="text-muted">- Click to get free training or proceed to Social Suite dashboard.</span></h3>
            </div>
            <!-- /.box-header -->				
                    <div class="box-body">

                        <div class="col-md-6">
							
<div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="//suite.social/src/dist/img/avatar04.png" alt="User profile picture">

              <h3 class="profile-username text-center">Name Here</h3>

              <p class="text-muted text-center">Social Suite User</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Your visitors so far:</b> <span class="pull-right badge bg-green"><?php echo $total_visits;?></span>
                </li>
                <li class="list-group-item">
                  <b>You will need:</b> <span class="pull-right badge bg-green"><?php echo $pending_counts; ?></span>
                </li>
                <li class="list-group-item">
                  <b>More visitors out of:</b> <span class="pull-right badge bg-green"><?php echo $value['visitor_target']; ?></span>
                </li>
              </ul>		  

			  <p><a href="<?php echo $value['url'];?>" class="btn btn-success btn-lg btn-block">CLICK HERE FOR FREE TRAINING! <i class="fa fa-arrow-right"></i></a></p>

            </div>
            <!-- /.box-body -->
          </div>							
														
							<!--<p><div class="fb-like" data-href="https://www.facebook.com/<?php echo $fbpage; ?>" data-colorscheme="dark" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>&nbsp;&nbsp;<div class="g-follow" data-annotation="bubble" data-height="24" data-href="<?php echo $gppage; ?>" data-rel="publisher"></div></p><br>-->
							
                        </div>

                        <div class="col-md-6">		
						<img width="100%" src="//suite.social/images/screen/dashboard.jpg" alt="Dashboard">
						<p><a href="//suite.social/dashboard.php" class="btn btn-primary btn-lg btn-block">OR GO TO DASHBOARD <i class="fa fa-arrow-right"></i></a></p>							
                        </div>

                    </div>
                </div>				
				
            </div>						
        </div>
	
              <?php 
			  $reset_visits=$sharelock->header($value['id'],$value['ip'],$string,$reset='1');  
              }            
            }

          ?>
   
<!-- =================FOOTER====================== -->				

        <script>
            // Messenger popup
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '102018820150735',
                    xfbml: true,
                    version: 'v2.6'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            // initialize Account Kit with CSRF protection
            AccountKit_OnInteractive = function () {
                AccountKit.init(
                        {
                            appId: 102018820150735,
                            state: "abcd",
                            version: "v1.1"
                        }
                //If your Account Kit configuration requires app_secret, you have to include ir above
                );
            };

            // login callback
            function loginCallback(response) {
                console.log(response);
                if (response.status === "PARTIALLY_AUTHENTICATED") {
                    document.getElementById("code").value = response.code;
                    document.getElementById("csrf_nonce").value = response.state;
                    document.getElementById("my_form").submit();
                } else if (response.status === "NOT_AUTHENTICATED") {
                    // handle authentication failure
                    console.log("Authentication failure");
                } else if (response.status === "BAD_PARAMS") {
                    // handle bad parameters
                    console.log("Bad parameters");
                }
            }
            // phone form submission handler
            function phone_btn_onclick() {
                // you can add countryCode and phoneNumber to set values
                AccountKit.login('PHONE', {}, // will use default values if this is not specified
                        loginCallback);
            }
            // email form submission handler
            function email_btn_onclick() {
                // you can add emailAddress to set value
                AccountKit.login('EMAIL', {}, loginCallback);
            }
            // destroying session
            function logout() {
                document.location = 'logout.php';
            }

        </script>
        <script type="text/javascript">
            function popuplogin(url)
            {
                var w = 800;
                var h = 600;
                var title = 'Social login';
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
                //window.open(url, '_self', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

            }
//            window.opener.location.replace('index.php?msg=success');
//            window.close();

        </script>
		
<!--    </body>
</html>
<script type="text/javascript" src="assets/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>-->
		
<!-- jQuery 3 -->
<script src="//suite.social/src/bower_components/jquery/dist/jquery.min.js"></script>

<?php include('../footer.php');?>