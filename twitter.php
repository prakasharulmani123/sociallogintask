<?php

session_start();
include_once("./app/twitteroauth/vendor/autoload.php");

use Abraham\TwitterOAuth\TwitterOAuth;

$config = array(
//    'consumer_key' => 'VhRAQoTDLecOrgRHHOWg13tVn',
//    'consumer_secret' => 'Jbt6L4gwL47LBx0X7zqGeww2X7FbwXVJekL7sxneSUPnzFGJbn',
    'consumer_key' => 'KqBOZd0dIP1MHBnI8mEaUKLFx',
    'consumer_secret' => 'OMQ5SZHGXu50eLjEST6ssGFMFzofCeLTcZNxisJxDXMa2VUkmA',
    'url_callback' => 'https://suite.social/login/twitter.php',
);
if (!isset($_GET['oauth_token']) && !isset($_GET['oauth_verifier'])) {
    $twitteroauth = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);
    $request_token = $twitteroauth->oauth('oauth/request_token', array('oauth_callback' => $config['url_callback']));
    if ($twitteroauth->getLastHttpCode() != 200) {
        throw new \Exception('There was a problem performing this request');
    }
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    $url = $twitteroauth->url(
            'oauth/authorize', [
        'oauth_token' => $request_token['oauth_token']
            ]
    );
    header('Location: ' . $url);
} else {

    $oauth_token = $_GET['oauth_token'];
    $oauth_verifier = $_GET['oauth_verifier'];
    $connection = new TwitterOAuth($config['consumer_key'], $config['consumer_secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $token = $connection->oauth(
            'oauth/access_token', [
        'oauth_verifier' => $oauth_verifier
            ]
    );
    $twitter = new TwitterOAuth($config['consumer_key'], $config['consumer_secret'], $token['oauth_token'], $token['oauth_token_secret']);
    $result = $twitter->get('account/verify_credentials', array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true'));  
    $user_data['user']['id'] = $result->id;
    $user_data['user']['displayName'] = $result->name;
    $user_data['user']['gender'] = "";
    $user_data['user']['email'] = $result->email;
    $user_data['user']['image'] = $result->profile_image_url;
    $user_data['user']['record_count'] = "";
    $user_data['records'] = "";
    $response[$result->id] = $user_data;


    require_once ('./include/class.database.php');
    $dbobj = new database();
    $values = array("data" => json_encode($response), "service_type" => 4);
    $dbobj->insert($values);
    ?>
    <script type="text/javascript">
        opener.location.href = 'https://suite.social/login/index.php?msg=success';
        close();
    </script>
<?php }
?>