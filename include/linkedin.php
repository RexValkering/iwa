<?php
require_once("includes.php");

function linkedin_get_request($url, $data, $debug = false) {
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " . linkedin_access_token(),
        "Content-type: application/json"
    );

    // Try with curl.
    $ch = curl_init();
    $query_params = http_build_query($data);
    curl_setopt($ch, CURLOPT_URL, $url . '?' . $query_params);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($debug) curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    $result = curl_exec($ch);

    if ($debug) { var_dump(curl_getinfo($ch, CURLINFO_HEADER_OUT));die(); }

    //close connection
    curl_close($ch);
    return $result;
}

function linkedin_get_profile() {
    $url = "https://api.linkedin.com/v1/people/~";
    $result = linkedin_get_request($url, ["format" => "json"]);
    return json_decode($result);
}

function linkedin_get_suggested_companies() {
    $url = "https://api.linkedin.com/v1/people/~/suggestions/to-follow/companies";
    $result = linkedin_get_request($url, ["format" => "json"]);
    return json_decode($result);
}

function linkedin_get_suggested_jobs() {
    $url = "https://api.linkedin.com/v1/people/~/suggestions/job-suggestions:(jobs)";
    $result = linkedin_get_request($url, ["format" => "json"]);
    return json_decode($result);
}