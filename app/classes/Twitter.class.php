<?php
//require_once '../twitteroauth/autoload.php';
echo __DIR__;
require_once __DIR__.'/../twitteroauth/vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

class Inviter {

    const CONSUMER_KEY = 'lp6wY3cuy64LjMsZCiNWw';
    const CONSUMER_SECRET = 'r4FgQAmYyJO83CS6fnlONB93wHsm6ySzTWZCLL2ZwiE';
//    const OAUTH_CALLBACK = 'http://suite.social/promo/OLD/demo_twitter.php?action=auth';
    const OAUTH_CALLBACK = 'hhttp://localhost/promo/twitter.php';

    //ACCOUNT TO FOLLOW = "Socialgrower"

    public function get_auth_url() {
        $connection = new TwitterOAuth(self::CONSUMER_KEY, self::CONSUMER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => self::OAUTH_CALLBACK));

        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        return $url;
    }
    public function do_auth() {
        $connection = new TwitterOAuth(self::CONSUMER_KEY, self::CONSUMER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => self::OAUTH_CALLBACK));

        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        header("Location: ". $url);
    }

    public function set_user_connection() {
        if (isset($_SESSION['oauth_token'])) {
            $request_token = [];
            $request_token['oauth_token'] = $_SESSION['oauth_token'];
            $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

            if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
                echo "Abort! Something is wrong";
                return;
            }

            $connection = new TwitterOAuth(self::CONSUMER_KEY, self::CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
            $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

            if (!empty($access_token)) {
                $_SESSION['access_token'] = $access_token;
                return true;
            } else {
                return null;
            }
        }
    }

    private function get_user_connection() {
        if (!isset($_SESSION['access_token'])) {
            return null;
        }
        $access_token = $_SESSION['access_token'];
        $connection = new TwitterOAuth(self::CONSUMER_KEY, self::CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $connection->setTimeouts(60, 60);

        return $connection;
    }

    public function get_user_followers() {
        $connection = $this->get_user_connection();
        if (!empty($connection)) {
            $friends = $connection->get("followers/list");
            return $friends;
        } else {
            return null;
        }
    }

    public function send_user_message($post) {
        $connection = $this->get_user_connection();
        if (!empty($connection)) {
            if (isset($_POST['message']) && isset($_POST['user'])) {
                $message = $_POST['message'];
                $user = $_POST['user'];
                $user_id = explode(",", $user['id']);
                $user_name = explode(",", $user['name']);

                // iterate over users
                for ($i = 0; $i < count($user_id); $i++) {
                    $response = $connection->post("direct_messages/new", [ 'id' => $user_id[$i], 'screen_name' => $user_name[$i], 'text' => $message]);
                }
                if (!empty($response->id))
                    $res = array('success' => true);
                else
                    $res = array('success' => false);
            }
            return $res;
        }
        else {
            return null;
        }
    }

}
$inviter = new Inviter();
