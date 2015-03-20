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

$xml = simplexml_load_string($result);

if (((string) $xml->results->result) == "") {
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

// Convert data to a useful format.
$json = sparql_result_to_json($xml);

exit($json);

function traverse_xml($xml, $spaces) {
    for ($i = 0; $i < $spaces; $i++)
        echo "&nbsp;";
    echo $xml->getName() . "<br />";
    if ($xml->children()) {
        foreach ($xml->children() as $child) {
            traverse_xml($child, $spaces + 1);
        }
    }
}

/**
 *  Convert a SPARQL XML result to a JSON object.
 */
function sparql_result_to_json($xml) {
    $final_array = array();
    foreach ($xml->results as $results) {
        $result_array = array();
        foreach ($results->result as $result) {
            $key = htmlspecialchars_decode($result->binding[0]->uri);    
            $value = $result->binding[1];
            if ($value->uri != "") 
                $result_array[(string) $key] = (string) $value->uri;
            else
                $result_array[(string) $key] = (string) $value->literal;
        }
        $final_array[] = $result_array;
    }
    return json_encode($final_array);
}

//exit(json_encode(glassdoor_get_company($_GET['name'])));