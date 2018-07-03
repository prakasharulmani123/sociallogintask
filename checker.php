<?php

error_reporting(0);
error_reporting(E_ALL);

ob_start();
session_start();

$base_url = "http://www.ekaminfotech.com/login/";

/// SOCIAL SUITE LOGIN CHECK
if (!isset($_SESSION['dashboard_uid'])) {
    session_destroy(); 
    header("Location: $base_url");
}

echo "Name : ".$_SESSION['name'];


?>