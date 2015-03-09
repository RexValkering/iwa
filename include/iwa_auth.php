<?php
// Start session.
session_start();

$page = $_SERVER['SCRIPT_NAME'];
$login_required = ["/application.php"];
$logout_required = ["/index.php"];

/**
 * By default, pages are not redirected and do not require a login.
 * Is there a more secure way to do this without making developing a pain?
 */
if (in_array($page, $logout_required)) {
    // Only logged out users can access this page.
    if (is_logged_in()) {
        header("Location: application.php");
        exit(0);
    }
}

else if (in_array($page, $login_required)) {
    // This page requires a login.
    if (!is_logged_in()) {
        header("Location: index.php");
        exit(0);
    }
}

/**
 *  Whether the user is logged into the application. This does not mean the
 *  LinkedIn key is still valid: it may be expired or missing.
 */
function is_logged_in() {
    return isset($_SESSION["application_id"]);
}

/**
 *  Whether the user has valid LinkedIn authentication.
 */
function linkedin_is_authenticated() {
    $date = linkedin_expiry_date();

    // Check both code and expiration date.
    if (!linkedin_auth_code() || !$date || $date < date())
        return false;
    return true;
}

function linkedin_authenticate($auth) {
    $date = new DateTime("+7 weeks");
    $_SESSION['linkedin_auth_code'] = $_GET['code'];
    $_SESSION['linkedin_expiry_date'] = $date->format('Y-m-d');

    // Give a temporary, invalid application id.
    // TODO: implement id assignment.
    if (!is_logged_in()) {
        $_SESSION["application_id"] = "tmp";
    }
}

/** 
 *  Get the linkedin authentication code.
 */
function linkedin_auth_code() {
    return (isset($_SESSION["linkedin_auth_code"]) ? $_SESSION["linkedin_auth_code"] : false);
}

/**
 *  Get the linkedin auth state code, the one we gave to the user.
 */
function linkedin_auth_state() {
    return (isset($_SESSION["linkedin_auth_state"]) ? $_SESSION["linkedin_auth_state"] : false);
}

/**
 *  Get the linkedin expiry date.
 */
function linkedin_expiry_date() {
    return (isset($_SESSION["linkedin_expiry_date"]) ? $_SESSION["linkedin_expiry_date"] : false);
}

/**
 *  Create and return a LinkedIn login url for the front page.
 */
function linkedin_login_url() {

    // Get the auth state.
    $auth = linkedin_auth_state();
    if ($auth === false) {
        $auth = md5(rand());
        $_SESSION["linkedin_auth_state"] = $auth;
    }

    // Formatted request uri.
    $requri = preg_replace('#[^/]*$#', '', $_SERVER['REQUEST_URI']);

    // Array of (extra) get parameters to pass.
    $data = [
        'client_id' => '759ovlctqc3v62',
        'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $requri . 'auth/linkedin.php',
        'state' => $auth
    ];

    // The URL to append to.
    $url = "https://www.linkedin.com/uas/oauth2/authorization?response_type=code";

    // Append all data to URL as get parameters.
    foreach ($data as $key => $value)
        $url .= '&amp;' . $key . '=' . $value;

    // Return result.
    return $url;
}