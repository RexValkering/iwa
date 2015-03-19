<?php
require_once("../include/includes.php");
$companies = linkedin_get_suggested_companies();

// foreach ($companies->values as $company) {
//     linkedin_company_to_rdf($company);
// }

exit(json_encode(linkedin_get_suggested_companies()));