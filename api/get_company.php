<?php
require_once("../include/includes.php");

if (!isset($_GET['name']))
    return;

exit(json_encode(glassdoor_get_company($_GET['name'])));