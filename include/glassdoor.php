<?php

function glassdoor_get_request($url, $data) {
    // $headers = array(
    //     "Accept: application/json",
    //     "Authorization: Bearer " . linkedin_access_token(),
    //     "Content-type: application/json"
    // );

    // Try with curl.
    $ch = curl_init();
    $query_params = http_build_query($data);
    curl_setopt($ch, CURLOPT_URL, $url . $query_params);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //echo $url . $query_params;

    $result = curl_exec($ch);

    //close connection
    curl_close($ch);
    return $result;
}

function glassdoor_get_company($name) {
    $url = "http://api.glassdoor.com/api/api.htm?";
    $data = array(
        "t.p" => glassdoor_partner_id(),
        "t.k" => glassdoor_partner_key(),
        "format" => "json",
        "userip" => get_ip_address(),
        "useragent" => $_SERVER['HTTP_USER_AGENT'],
        "v" => "1",
        "action" => "employers",
        "q" => $name,
        "pn" => 1
    );

    return json_decode(glassdoor_get_request($url, $data));
}