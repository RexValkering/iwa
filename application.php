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
    <script src="js/application.js"></script>
</head>
<body>
    <div id="application" class="container">
        <div class="row">
            <ul id="jobs">
                <!-- filled by js -->
            </ul>
            <div id="job" style="display:none">
                <h2 id="title">Job title?</h2>
                <p  id="description"></p>

                <h2 id="about"></h2>

                <table id="dbpedia">
                    
                </table>

                <h2>More information</h2>

                <table id="glassdoor">

                </table>
            </div>
            <div class="text-center" id="actions">
                <a href="<?php echo site_root(); ?>auth/logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>