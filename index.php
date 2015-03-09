<?php 
require_once('include/includes.php');

$url = linkedin_login_url();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/iwa.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="landing" class="container">
    <div class="row">
        <div class="col-xs-12 text-center">
        <h3>Intelligent application title</h3>
        <?php display_error(); ?>
        </div>
    </div>
    <!-- Sign in with LinkedIn -->
    <div class="row top-buffer">
        <div class="col-xs-12">
        <a href="<?php echo $url; ?>"><div id="linkedin-login"></div></a></div>
    </div>

    <!-- Sign in with account -->
    <div class="row top-buffer">
        <div class="col-xs-12 text-center">
        <a><button id="application-login" type="button" class="btn btn-default">Sign in with your application account</button></a></div>
    </div>

    <div class="row top-buffer-large">
        <div class="col-xs-12 text-center">Disclaimer | Privacy Policy | Contact | About</div>
    </div>
</div>
</body>
</html>