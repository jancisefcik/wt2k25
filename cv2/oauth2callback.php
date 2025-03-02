<?php

session_start();

require_once 'vendor/autoload.php';
require_once '../../config.php';

use Google\Client;

$client = new Client();

// Required, call the setAuthConfig function to load authorization credentials from
// client_secret.json file. The file can be downloaded from Google Cloud Console.
$client->setAuthConfig('../../client_secret.json');
$redirect_uri = "ENTER_YOUR_REDIRECT_URI_HERE"; // Redirect URI for the OAuth2 callback. Must match the one in the Google Cloud Console.
$client->setRedirectUri($redirect_uri);

// Required, to set the scope value, call the addScope function.
// Scopes define the level of access that the application is requesting from Google.
$client->addScope(["email", "profile"]);
// Enable incremental authorization. Recommended as a best practice.
$client->setIncludeGrantedScopes(true);

// Recommended, offline access will give you both an access and refresh token so that
// your app can refresh the access token without user interaction.
$client->setAccessType("offline");

// Generate a URL for authorization as it doesn't contain code and error
if (!isset($_GET['code']) && !isset($_GET['error'])) {
    // Generate and set state value
    $state = bin2hex(random_bytes(16));
    $client->setState($state);
    $_SESSION['state'] = $state;

    // Generate a url that asks permissions.
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

// User authorized the request and authorization code is returned to exchange access and
// refresh tokens. If the state parameter is not set or does not match the state parameter in the
// authorization request, it is possible that the request has been created by a third party and the user
// will be redirected to a URL with an error message.
// If the authorization was successful, the response URI will contain an authorization code.
if (isset($_GET['code'])) {
    // Check the state value
    if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['state']) {
        die('State mismatch. Possible CSRF attack.');
    }

    // Get access and refresh tokens (if access_type is offline)
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    /** Save access and refresh token to the session variables.
     * TODO: In a production app, you likely want to save the
     *              refresh token in a secure persistent storage instead. */
    $_SESSION['access_token'] = $token;
    $_SESSION['refresh_token'] = $client->getRefreshToken();

    $_SESSION['loggedin'] = true;  // User is logged in / authenticated - set custom session variable.

    // TODO: Implement a mechanism to save login information - user_id, login_type, email, fullname - to database.

    $redirect_uri = 'ENTER_YOUR_REDIRECT_URI_HERE'; // Redirect to the restricted page or dashboard.
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
// An error response e.g. error=access_denied
if (isset($_GET['error'])) {
    echo "Error: " . $_GET['error'];
}
