<?php
/**
 *  This script is calling by LinkedIn after a user attempts to login and
 *  authenticate our application.
 */

require_once("../include/includes.php");

// Ensure all required parameters are set.
if (!isset($_GET['code']) || !isset($_GET['state'])) {
    if (isset($_GET['error']))
        $_SESSION['error'] = $_GET['error'];
    if (isset($_GET['error_description']))
        $_SESSION['error_description'] = $_GET['error_description'];
    header("Location: /index.php");
    exit(0);
}

// Ensure the state parameters matches the one that was set.
$state = $_GET['state'];
if ($state != linkedin_auth_state()) {
    $_SESSION['error'] = "state_invalid";
    $_SESSION['error_description'] = "Missing or invalid auth state key.";
    header("Location: /index.php");
    exit(0);
}

// Now retrieve an access token.
linkedin_authenticate($_GET['code']);

$url = linkedin_token_url();
$data = linkedin_token_data();

// Try with curl.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($data));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$result = json_decode($result);

if (!isset($result->access_token)) {
    // Set error and type.
    $_SESSION['error_description'] = $result->error_description;
    $_SESSION['error'] = $result->error;
    header("Location: " . site_root() . "auth/logout.php");
    exit(0);
}
$_SESSION["linkedin_access_token"] = $result->access_token;

//close connection
curl_close($ch);

// Source: http://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
// use key 'http' even if you send the request to https://...
// $options = array(
//     'http' => array(
//         'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//         'method'  => 'POST',
//         'content' => http_build_query($data),
//     ),
// );
// $context  = stream_context_create($options);
// $result = file_get_contents($url, false, $context);

// print_r($result);

header("Location: " . site_root() . "application.php");
exit(0);