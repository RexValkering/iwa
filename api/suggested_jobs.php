<?php
require_once("../include/includes.php");

// Request jobs.
$jobs = linkedin_get_suggested_jobs();
$response = json_encode($jobs);

// // Send data before continuing the process.
// ob_end_clean();
// header("Connection: close");
// ignore_user_abort(true);
// ob_start();
// echo $response;
// header("Content-Length: " . mb_strlen($response));
// ob_end_flush();
// flush();

// Now get all companies and add them to the rdf store.
$jobs = $jobs->jobs->values;

foreach ($jobs as $job) {
    linkedin_company_to_rdf($job->company);
}

exit($response);