<?php

/**
 *          RAFAEL FERREIRA � 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** Google Youtube **
 * ------------------------------------------------------------------------
 */
require_once("google/vendor/autoload.php");
require_once("Handling.class.php");

class Youtube {
		

    public static function get_email() {
        global $Configuration;
			
			$client = new Google_Client();
			$client->setClientId($Configuration["google_youtube_client_id"]);
			$client->setClientSecret($Configuration["google_youtube_client_secret"]);
			$client->setRedirectUri($Configuration["google_youtube_redirect_uri"]);
			
			$scopes = urlencode('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/youtube');
            
			$client->setScopes($scopes);
        
			$youtube = new Google_Service_YouTube($client);
        
		
        if (isset($_GET['code'])) {			
			
			$client->authenticate($_GET['code']);
			
            $token = (object) $client->getAccessToken();				
            //echo  "<pre>"; print_r($token);  die;
            if (isset($token->id_token)) {
				
				$suscription_status ="";
				
				try {
					// Identify the resource being subscribed to by specifying its channel ID
					// and kind.
					$resourceId = new Google_Service_YouTube_ResourceId();
					$resourceId->setChannelId($Configuration["google_youtube_channel_id"]);
					$resourceId->setKind('youtube#channel');
		
					 // Create a snippet object and set its resource ID.
					$subscriptionSnippet = new Google_Service_YouTube_SubscriptionSnippet();
					$subscriptionSnippet->setResourceId($resourceId);

					// Create a subscription request that contains the snippet object.
					$subscription = new Google_Service_YouTube_Subscription();
					$subscription->setSnippet($subscriptionSnippet);

					// Execute the request and return an object containing information
					// about the new subscription.
					$subscriptionResponse = $youtube->subscriptions->insert('id,snippet',
						$subscription, array());
					
					$suscription_status ="Subscribed Successfully";
				
				} catch (Google_Service_Exception $e) {
					$suscription_status = sprintf('<p>A service error occurred: <code>%s</code></p>',
											htmlspecialchars($e->getMessage()));
				}
				 catch (Google_Exception $e) { 
					$suscription_status	= sprintf('<p>An client error occurred: <code>%s</code></p>',
											htmlspecialchars($e->getMessage()));
				}
				
				
				
                $request = Handling::curlHttpRequest("https://www.googleapis.com/oauth2/v1/userinfo??alt=json&access_token=" . $token->access_token);
                $r = Handling::curlHttpRequest("https://www.googleapis.com/plus/v1/people/me??alt=json&access_token=" . $token->access_token);
                $r = json_decode($r);
                $bdate = isset($r->birthday) ? $r->birthday : '';
                return json_encode(array("status" => "success", "data" => array("profile" => json_decode($request), "subscription" => $suscription_status, 'birthday' => $bdate)));
            }
        }

        #Auth URL        
        $url = "https://accounts.google.com/o/oauth2/auth?client_id=" . $Configuration["google_youtube_client_id"] . "&response_type=code&scope=" . $scopes . "&redirect_uri=" . $Configuration["google_youtube_redirect_uri"] . "&access_type=online&approval_prompt=auto";
        return json_encode(array("status" => "url", "data" => array("url" => $url)));
    }


}
