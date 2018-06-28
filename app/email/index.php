<?php
/**
 *          RAFAEL FERREIRA © 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** Facebook **
 * ------------------------------------------------------------------------
 */
require_once("../Configuration.php");

if(!$ActiveServices["email"]){
    exit("Service not active!");
}

$email = urldecode(filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL));

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
	header("Location: ".$responsePage["bad_email"]);
}else{
	require_once("../classes/Handling.class.php");
	Handling::handling_request_with_confirmation($email, NULL);
}