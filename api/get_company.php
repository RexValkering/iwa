<?php
require_once("../include/includes.php");

if (!isset($_GET['id']) && !isset($_GET['name'])) {
    exit("Please pass an id or name");
}

$result = Null;
if (isset($_GET['id'])) {
    $result = stardog_get_company_by_id($_GET['id']);
}
else {
    $result = stardog_get_company_by_name($_GET['name']);

}
print_r($result);
exit;
if (true) {
    // No results were found.
    echo "No results found.";

    // Find company in Glassdoor, export data to RDF store.
    $company = glassdoor_get_company($_GET['name']);
    glassdoor_company_to_rdf($company->response->employers[0]);

    // Execute query again.
    $result = stardog_get_company_by_name($_GET['name']);
    $xml_result = $xml->results->result;

    if (((string) $xml_result) == "") {
        // Nothing happens.
        echo "Cannot find any results in Glassdoor.";
        exit;
    }
}


exit($result);

