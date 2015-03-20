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
        <h3>Intelligent Web Applications</h3>
        <?php display_error(); ?>
        </div>
    </div>
    <!-- Sign in with LinkedIn -->
    <div class="row top-buffer">
        <div class="col-xs-12">
        <a href="<?php echo $url; ?>"><div id="linkedin-login"></div></a></div>
    </div>

    <!-- Sign in with LinkedIn -->
    <div class="row top-buffer">
        <div class="col-xs-12 text-center">
       </div>
    </div>

    <!-- Sign in with LinkedIn -->
    <div class="row top-buffer">
        <div class="col-xs-12 text-center">
        Created by Robin van der Markt, Rex Valkering and Sijmen van der Willik.
       </div>
    </div>


</div>
</body>
</html>