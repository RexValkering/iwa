<?php
require_once("include/includes.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/iwa.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/iwa.js"></script>
</head>
<body>
<div id="application" class="container">
  
<div class="row">
<div class="col-xs-12 text-center" id="iwa-profile-link">
<a href="#" onclick="fill_profile(); return false;">Load profile</a>
</div>
</div>

<div id="companies">
<div class="row">
<div class="col-xs-12 text-center">
<a href="#" onclick="fill_companies(); return false;">Load companies</a>
</div>
</div>
</div>

<div class="row">
<div class="col-xs-12 text-center">
<a href="<?php echo site_root(); ?>auth/logout.php">Logout</a></div>
</div>
</div>
</body>
</html>