<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to restricted page.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: restricted.php");
    exit;
}

require_once "../../config.php";
require_once 'vendor/autoload.php';
require_once 'utilities.php';

use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

// Redirect users to outh2call.php which redirects users to Google OAuth 2.0
$redirect_uri = "https://node8.webte.fei.stuba.sk/cv2/oauth2callback.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TODO: Implement login credentials verification.
    // TODO: Implement a mechanism to save login information - user_id, login_type, email, fullname - to database.

    $sql = "SELECT id, fullname, email, password, 2fa_code, created_at FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $_POST["email"], PDO::PARAM_STR);
    $errors = "";

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            // User exists, check password.
            $row = $stmt->fetch();
            $hashed_password = $row["password"];

            if (password_verify($_POST['password'], $hashed_password)) {
                // Password is correct.
                $tfa = new TwoFactorAuth(new EndroidQrCodeProvider());
                if ($tfa->verifyCode($row["2fa_code"], $_POST['2fa'], 2)) {
                    // Password and code are correct, user authenticated.

                    // Save user data to session.
                    $_SESSION["loggedin"] = true;
                    $_SESSION["fullname"] = $row['fullname'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["created_at"] = $row['created_at'];

                    // Redirect user to restricted page.
                    header("location: restricted.php");
                }
                else {
                    $errors = "Neplatný kod 2FA.";
                }
            } else {
                $errors = "Nesprávne meno alebo heslo.";
            }
        } else {
            $errors = "Nesprávne meno alebo heslo.";
        }
    } else {
        $errors = "Ups. Niečo sa pokazilo...";
    }

    unset($stmt);
    unset($pdo);
}

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prihlásenie</title>

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
    </style>
</head>
<body>
<header>
    <hgroup>
        <h1>Prihlásenie</h1>
        <h2>Prihlasenie registrovaného používateľa</h2>
    </hgroup>
</header>
<main>
    <?php if (isset($errors)) {
        echo "<strong style='color: red'>$errors</strong>";
    } ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <label for="email">
            E-Mail:
            <input type="text" name="email" value="" id="email" required>
        </label>
        <br>
        <label for="password">
            Heslo:
            <input type="password" name="password" value="" id="password" required>
        </label>
        <br>

        <!-- TODO: Use JavaScript to hide/show the 2FA field after successfull password enter,
                   and only after completing the 2FA code, the user is logged in. -->

        <label for="2fa">
            2FA kód:
            <input type="number" name="2fa" value="" id="2fa" required>
        </label>

        <button type="submit">Prihlásiť sa</button>
        <br>
        <p>Alebo sa prihláste pomocou <a href="<?php echo filter_var($redirect_uri, FILTER_SANITIZE_URL) ?>">Google konta</a></p>

        <!-- TODO: Create a "I forgot password"/"Reset my password" option -->

    </form>
    <p>Nemáte vytvorené konto? <a href="register.php">Zaregistrujte sa tu.</a></p>
</main>
</body>
</html>