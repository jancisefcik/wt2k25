<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../../config.php';
require_once 'vendor/autoload.php';

use Google\Client;

$client = new Client();
// Required, call the setAuthConfig function to load authorization credentials from
// client_secret.json file. The file can be downloaded from Google Cloud Console.
$client->setAuthConfig('../../client_secret.json');


// User granted permission as an access token is in the session.
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);

    // Get the user profile info from Google OAuth 2.0.
    $oauth = new Google\Service\Oauth2($client);
    $account_info = $oauth->userinfo->get();

    
    $_SESSION['fullname'] = $account_info->name;
    $_SESSION['gid'] = $account_info->id;
    $_SESSION['email'] = $account_info->email;

}

// TODO: Provide the user with the option to temporarily disable or reset 2FA.
// TODO: Provide the user with the option to reset the password.

?>
<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zabezpečená stránka</title>

    <style>
        html {
            max-width: 70ch;
            padding: 3em 1em;
            margin: auto;
            line-height: 1.75;
            font-size: 1.25em;
        }
        h1,h2,h3,h4,h5,h6 {
            margin: 3em 0 1em;
        }
        p,ul,ol {
            margin-bottom: 2em;
            color: #1d1d1d;
            font-family: sans-serif;
        }
        span, .err {
            color: red;
        }
    </style>
</head>

<body>
    <header>
        <hgroup>
            <h1>Zabezpečená stránka</h1>
            <h2>Obsah tejto stránky je dostupný len po prihlásení.</h2>
        </hgroup>
    </header>
    <main>
        <!-- TODO: This is just a demo. This information will be displayed usually in header. -->
        <h3>Vitaj <?php echo $_SESSION['fullname']; ?></h3>
        <p><strong>e-mail:</strong> <?php echo $_SESSION['email']; ?></p>
        
        <?php if (isset($_SESSION['gid'])) : ?>
            <p><strong>Si prihlásený cez Google účet, ID:</strong> <?php echo $_SESSION['gid']; ?></p>
        <?php else : ?>
            <p><strong>Si prihlásený cez lokálne údaje.</strong></p>
            <p><strong>Dátum vytvonia konta:</strong> <?php echo $_SESSION['created_at'] ?></p>
        <?php endif; ?>

        <p><a href="logout.php">Odhlásenie</a> alebo <a href="index.php">Úvodná stránka</a></p>

    </main>
</body>

</html>