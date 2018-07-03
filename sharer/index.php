<?php

	///////////////////////// SHARELOCK HEADER - START //////
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
    '0'=>array("id"=>"1","visitor_target"=>"5","url"=>"https://YourWebsite.com/Download.zip","theme"=>"","ip"=>"1","reset"=>"1"),
    );
    $sharelock = new sharelock();
	
    //current url of file
    $uri = $_SERVER['REQUEST_URI'];
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];		

	///////////////////////// SHARELOCK HEADER - END //////
	
	
	///////////////////////// SHARELOCK SETTINGS - START //////	
		
    $banner = 'https://dummyimage.com/1400x500/cccccc/000000.png&text=Replace+your+product+image+here';	
    $logo = 'https://dummyimage.com/80x80/cccccc/000000.png&text=Logo';
    $headline = 'Your Headline Here';
    $caption = 'Your Caption Here';
	$website = 'http://www.YourWebsite.com';
	$action = 'download';	
	$network = 'facebook';
	$shareurl = 'https://www.facebook.com/sharer.php?u=';
	
	$_SESSION['banner'] = $banner;
	$_SESSION['logo'] = $logo;
	$_SESSION['headline'] = $headline;
	$_SESSION['caption'] = $caption;
	$_SESSION['website'] = $website;
	$_SESSION['action'] = $action;	
	$_SESSION['network'] = $network;
	$_SESSION['shareurl'] = $shareurl;	
	
	///////////////////////// SHARELOCK SETTINGS - END //////	

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <!--<html manifest="cache.appcache" class="no-js"> <!--<![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>

    <!-- Title -->
    <title>Social Share - <?php echo $headline; ?></title>

    <!-- Meta Data -->
    <meta name="title" content="<?php echo $headline; ?>">
    <meta name="description" content="<?php echo $caption; ?>">
    <meta name="keywords" content="<?php echo $network; ?> share, <?php echo $network; ?> content locker, <?php echo $network; ?> friend inviter, <?php echo $network; ?> share counter, <?php echo $network; ?> social locker, <?php echo $network; ?> social marketing, <?php echo $network; ?> social offer, <?php echo $network; ?> social promotion, <?php echo $network; ?> social referral, <?php echo $network; ?> social sharing, <?php echo $network; ?> link share, <?php echo $network; ?> visitor counter">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <meta name="revisit-after" content="14 days">
    <meta name="author" content="<?php echo $current_url; ?>">	
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />	
		
	<!-- Google Plus -->
	<!-- Update your html tag to include the itemscope and itemtype attributes. -->
	<!-- html itemscope itemtype="//schema.org/{CONTENT_TYPE}" -->
	<meta itemprop="name" content="<?php echo $headline; ?>">
	<meta itemprop="description" content="<?php echo $caption; ?>">
	<meta itemprop="image" content="<?php echo $banner; ?>">	
	
	<!-- Twitter -->
	<meta name="twitter:card" content="<?php echo $headline; ?>">
	<meta name="twitter:title" content="<?php echo $headline; ?>">
	<meta name="twitter:description" content="<?php echo $caption; ?>">
	<meta name="twitter:image:src" content="<?php echo $banner; ?>">
	
	<!-- Open Graph General (Facebook & Pinterest) -->
	<meta property="og:url" content="<?php echo $current_url; ?>">
	<meta property="og:title" content="<?php echo $headline; ?>">
	<meta property="og:description" content="<?php echo $caption; ?>">
	<meta property="og:image" content="<?php echo $banner; ?>">
	<meta property="og:type" content="product">

	<!-- Open Graph Article (Facebook & Pinterest) -->
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
	
    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
	<script src="//use.fontawesome.com/9b98e0b658.js"></script>
	
    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/normalize.css">	
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/responsive.css">
	<link rel="stylesheet" href="css/social-buttons.css">
	
    <!-- JQuery -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<style type="text/css">

html {
	position: relative;
	min-height: 100%;
}	

body {
	margin: 0 0 30px; /* Height of the footer */
	overflow-x: hidden;
}	

h1 {
	font-size: 4.7vw;
}

#footer {
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 30px;
    color: #fff;
	text-align: center;
    overflow: hidden; 	
}

#counter {
	width: 60%;
	color: #fff;
	font-size: 5.7vw;
	font-weight: bold;
	padding:5px; 
	border:5px solid #fff;
	border-radius: 15px;
	background: #111111;
	background: -moz-linear-gradient(top,  #111111 0%, #2c2c2c 50%, #2c2c2c 50%, #000000 50%, #111111 100%);
	background: -webkit-linear-gradient(top,  #111111 0%,#2c2c2c 50%,#2c2c2c 50%,#000000 50%,#111111 100%);
	background: linear-gradient(to bottom,  #111111 0%,#2c2c2c 50%,#2c2c2c 50%,#000000 50%,#111111 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#111111', endColorstr='#111111',GradientType=0 );
}
	
input {
    line-height: normal;
    color: #1f1f1f;
    font-size: 30px;
}

.form-control {
    font-size: 25px;
    color: #1f1f1f;
    background-color: #fff;
}

</style>	
	
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="//browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!------------------------EDIT THIS TEMPLATE!------------------------>	

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
			   
<main id="dashboard">
    <div class="container">
	
        <div style="padding: 10px; background-color: #262626; border: #616161 2px solid;" class="row">		        
                <div align="center" class="page-title">
				<a href="<?php echo $website; ?>" target="_blank"><img src="<?php echo $banner; ?>" width="100%" alt="Banner"></a>
				<img style="margin-top: 30px" src="<?php echo $logo; ?>" width="80px" alt="Profile Image">
				<h4 style="color:#8ec657"><strong><?php echo $headline; ?></strong></h4>
				<h4><i>"<?php echo $caption; ?>"</i></h4>
				<a href="#how" data-toggle="collapse" class="btn btn-success"> HOW IT WORKS! <i class="fa fa-question-circle"></i></a>					
				<a href="<?php echo $website; ?>" target="_blank" class="btn btn-gray"> VISIT WEBSITE <i class="fa fa-link"></i></a>
				<a href="#embed" data-toggle="collapse" class="btn btn-success"> EMBED BUTTON <i class="fa fa-code"></i></a>

				<div id="embed" class="collapse">
			
				<div align="center">
				<br><br><img src="button.png" width="320px" alt="Button">

				<h4>Copy the button code for your website, blog or sales page.</h4>               	
				<textarea rows="3" class="form-control"><a href="<?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank"><img src="<?php echo $current_url; ?>button.png" width="320px" alt="Button"></a></textarea>				
	
				</div>

				</div>
															
				<div id="how" class="collapse">
				<h2>1. Share on <?php echo $network; ?></h2>
				<p>To unlock this offer, share with friends, followers or subscribers on <?php echo $network; ?>.</p>
				<h2>2. Check the counter daily</h2>
				<p>The "You will need" counter will decrease the more people visit your link (only one visit is recorded per person).</p>
				<h2>3. Unlock your offer</h2>
				<p>Once the "Visitors so far" counter reaches targeted number of visitors, you will see the "Congratulations" message then your offer will be unlocked. Please claim immediately since the counter may reset after your next visit.</p>				
				</div>	

				<h4 style="margin-top: 30px">Visitors so far:</h4>
				<div id="counter"><?php echo $total_visits;?></div> 				
				<h4>You will need:</h4>
				<div id="counter"><?php echo $pending_counts; ?></div> 				
				<h4>more visitor(s) out of</h4> 
				<div id="counter"><?php echo $value['visitor_target']; ?></div> 
				<h4>visitors to <?php echo $action; ?>. </h4>
				<br />				
				</div>
        </div>
		
        <br>
		
        <div align="center" class="row">
		
		<h1>Share on <?php echo $network; ?> to make it happen!</h1>
		<p><i>You must accept cookies in your browser.</i></p>
		<?php if($string == ''){ ?>
	
		<?php }else{ 
		$param='?';		
		$pos = strpos($current_url, $param);
		$endpoint = $pos + strlen($param);
		$newStr = substr($current_url,0,$endpoint );
		?>
				<p><input style="width: 100%;" type="text" value="<?php echo $newStr.'id'.$value['id'].'='.$myip_add; ?>" /></p>	
	<?php } ?>	
		<br>

        <div class="col-md-12 text-center">
            <div class="social-buttons">
                <a href="<?php echo $shareurl; ?><?php echo $current_url.'?id'.$value['id'].'='.$myip_add; ?>" target="_blank" class="btn btn-block btn-social btn-<?php echo $network; ?>">
                    <i class="fa fa-<?php echo $network; ?> fa-5x"></i> <h1>Share on <?php echo $network; ?></h1>					
                </a>				
            </div>			
        </div>			

        </div>
    </div>
</main>
 

<!------------------------EDIT LOCKED CONTENT------------------------>	
	  
<?php
                   
              }else
              { 
                //redirect to target url if counter is greater than target counter
              ?>

    <div class="container">
	
        <div style="padding: 10px; background-color: #262626; border: #616161 2px solid;" class="row">		        
                <div align="center" class="page-title">
				<a href="<?php echo $website; ?>" target="_blank"><img src="<?php echo $banner; ?>" width="100%" alt="Banner"></a>
				<img style="margin-top: 30px" src="<?php echo $logo; ?>" width="80px" alt="Profile Image">
                    <h4 style="color:#8ec657"><strong><?php echo $headline; ?></strong></h4>
					<h4><i>"<?php echo $caption; ?>"</i></h4>
					<div id="counter">Congratulations!<p style="font-size: 30px;">You've reached targeted visitor count of:</p><?php echo $value['visitor_target']; ?></div>
					<h4><i>"Now you can <?php echo $action; ?>!"</i></h4>
					<p><a href="<?php echo $value['url'];?>" class="btn btn-lg btn-success"> CLICK HERE TO <?php echo $action; ?>! <i class="fa fa-download"></i></a></p>
				</div>
        </div>
    </div>            
              <?php 
			  $reset_visits=$sharelock->header($value['id'],$value['ip'],$string,$reset='1');  
              }            
            }

          ?>

<br>
<br>		  

        <div align="center" id="footer">	
			
			<!--  Copyright Line -->
			<div class="copy">&copy; <?php echo date('Y'); ?> - <a href="<?php echo $website; ?>"><?php echo $website; ?></a> - All Rights Reserved.</div>
			<!--  End Copyright Line -->
	
		</div>

<!-- Contents End here  //.-->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </body>
</html>