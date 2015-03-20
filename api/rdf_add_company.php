<?php
require_once("../include/includes.php");

$obj = new stdClass();
$obj->id = 1009;
$obj->name = "IBM";

linkedin_company_to_rdf($obj);

if (!isset($_GET['name']))
    return;

$result = glassdoor_get_company($_GET['name']);
exit(glassdoor_company_to_rdf($result->response->employers[0]));