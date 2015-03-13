<?php
require_once("includes.php");

function linkedin_get_profile() {
    $url = "https://api.linkedin.com/v1/people/~";
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " . linkedin_access_token(),
        "Content-type: application/json"
    );

    // Try with curl.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    //close connection
    curl_close($ch);
    return $result;
}