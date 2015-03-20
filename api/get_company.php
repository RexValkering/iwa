<?php
require_once("../include/includes.php");

if (!isset($_GET['id']) && !isset($_GET['name'])) {
    exit("Please pass an id or name");
}

$result = Null;
if (isset($_GET['id'])) 
    $result = json_decode(stardog_get_company_by_id($_GET['id']));
else
    $result = json_decode(stardog_get_company_by_name($_GET['name']));

//print_r($result->results->bindings);
$array = sparql_result_to_array($result);
print_r($array);
//exit;

if ($array == [])
    exit(json_encode($array));

if (!isset($array["http://iwa.rexvalkering.nl/website"])) {

    // Find company in Glassdoor, export data to RDF store.
    $company = glassdoor_get_company($array['http://iwa.rexvalkering.nl/name']);
    print_r($company->response->employers[0]);
    glassdoor_company_to_rdf($company->response->employers[0]);

    // Execute query again.
    if (isset($_GET['id'])) 
        $result = json_decode(stardog_get_company_by_id($_GET['id']));
    else
        $result = json_decode(stardog_get_company_by_name($_GET['name']));
    $array = sparql_result_to_array($result);
}

exit(json_encode($array));

