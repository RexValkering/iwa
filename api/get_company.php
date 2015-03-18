<?php
require_once("../include/includes.php");

if (!isset($_GET['name']))
    return;

$result = stardog_get_company_by_name($_GET['name']);

$xml = simplexml_load_string($result);
traverse_xml($xml, 0);
print_r((string) $xml->results->result->binding->uri);

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

//exit(json_encode(glassdoor_get_company($_GET['name'])));