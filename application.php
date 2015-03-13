<?php
require_once("include/includes.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/iwa.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="application" class="container">
  
<div class="row">
<div class="col-xs-12 text-center">
<?php print_r(linkedin_get_profile()); ?>
</div>
</div>
<div class="row">
<div class="col-xs-12 text-center">
<a href="<?php echo site_root(); ?>auth/logout.php">Uitloggen</a></div>
</div>
</div>
</body>
</html>