<?php
require_once("../include/includes.php");

if (!isset($_GET['name']))
    return;

$result = glassdoor_get_company($_GET['name']);
exit(glassdoor_company_to_rdf($result->response->employers[0]));