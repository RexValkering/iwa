<?php
// Start session.
session_start();

$page = $_SERVER['SCRIPT_NAME'];
$login_required = ["application.php"];
$logout_required = ["index.php"];

/**
 * By default, pages are not redirected and do not require a login.
 * Is there a more secure way to do this without making developing a pain?
 */
if (in_array($page, $logout_required)) {
    // Only logged out users can access this page.
    if (is_logged_in()) {
        header("Location: " . site_root() . "application.php");
        exit(0);
    }
}

else if (in_array($page, $login_required)) {
    // This page requires a login.
    if (!is_logged_in()) {
        header("Location: " . site_root() . "index.php");
        exit(0);
    }
}

/**
 *  Make sure the linkedin and glassdoor xml files exist and are loaded.
 *  The __FILE__ is needed to make it work for all pages.
 */
global $linkedin, $glassdoor;

$linkedin = simplexml_load_file(dirname(__FILE__)."/../../linkedin.xml");
// If nonexistent, show an error.
if ($linkedin === false) { ?>
    <p class="text-warning">Error, could not find/read LinkedIn XML file.</p>
<?php }

$glassdoor = simplexml_load_file(dirname(__FILE__)."/../../glassdoor.xml");
// If nonexistent, show an error.
if ($glassdoor === false) { ?>
    <p class="text-warning">Error, could not find/read Glassdoor XML file.</p>
<?php }

/**
 *  Whether the user is logged into the application. This does not mean the
 *  LinkedIn key is still valid: it may be expired or missing.
 */
function is_logged_in() {
    return isset($_SESSION["application_id"]);
}

/**
 *  Get the glassdoor partner id.
 */
function glassdoor_partner_id() {
    global $glassdoor;
    return ($glassdoor ? (string) $glassdoor->partnerid : "");
}

/**
 *  Get the glassdoor partner key.
 */
function glassdoor_partner_key() {
    global $glassdoor;
    return ($glassdoor ? (string) $glassdoor->key : "");
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
 *  Get the linkedin client id.
 */
function linkedin_client_id() {
    global $linkedin;
    return ($linkedin ? (string) $linkedin->api_key : "");
}

/**
 *  Get the linkedin client secret.
 */
function linkedin_client_secret() {
    global $linkedin;
    return ($linkedin ? (string) $linkedin->api_secret : "");
}

/**
 *  Get the linkedin access token.
 */
function linkedin_access_token() {
    return (isset($_SESSION["linkedin_access_token"]) ? $_SESSION["linkedin_access_token"] : false);
}

/**
 *  Get the linkedin redirect uri (file that handles linkedin callbacks).
 */
function linkedin_redirect_uri() {
    return 'http://' . $_SERVER['SERVER_NAME'] . site_root() . 'auth/linkedin.php';
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

    // Array of (extra) get parameters to pass.
    $data = [
        'client_id' => linkedin_client_id(), //'759ovlctqc3v62',
        'redirect_uri' => linkedin_redirect_uri(),
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

/**
 *  Return a LinkedIn Request Token url for after logging in.
 */
function linkedin_token_url() {
    // Return result.
    return "https://www.linkedin.com/uas/oauth2/accessToken";
}

/**
 *  Create and return a LinkedIn Request Token parameter array.
 */
function linkedin_token_data() {
    // Check if user is authenticated.
    $code = linkedin_auth_code();
    if ($code === false)
        return [];

    // Array of POST parameters to pass.
    $data = [
        'grant_type' => urlencode('authorization_code'),
        'code' => urlencode($code),
        'redirect_uri' => linkedin_redirect_uri(),
        'client_id' => urlencode(linkedin_client_id()),
        'client_secret' => urlencode(linkedin_client_secret())
    ];

    return $data;
}