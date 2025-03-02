<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once '../../config.php';
require_once 'vendor/autoload.php';
require_once 'utilities.php';

use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = "";

    // Validate email
    if (isEmpty($_POST['email']) === true) {
        $errors .= "Nevyplnený e-mail.\n";
    }

    // TODO: validate if user entered correct e-mail format

    // Validate user existence
    if (userExist($pdo, $_POST['email']) === true) {
        $errors .= "Používateľ s týmto e-mailom už existuje.\n";
        die();
    }

    // Vaidate name and surname
    if (isEmpty($_POST['firstname']) === true) {
        $errors .= "Nevyplnené meno.\n";
    } elseif (isEmpty($_POST['lastname']) === true) {
        $errors .= "Nevyplnené priezvisko.\n";
    }

    // TODO: Implement name and surname length validation based on the database column length.
    // TODO: Implement name and surname allowed characters validation.


    // Validate password
    if (isEmpty($_POST['password']) === true) {
        $errors .= "Nevyplnené heslo.\n";
    }

    // TODO: Implement repeat password validation.
    // TODO: Sanitize and validate all user inputs.

    if (empty($errors)) {
        $sql = "INSERT INTO users (fullname, email, password, 2fa_code) VALUES (:fullname, :email, :password, :2fa_code)";

        $fullname = $_POST['firstname'] . ' ' . $_POST['lastname'];
        $email = $_POST['email'];
        $pw_hash = password_hash($_POST['password'], PASSWORD_ARGON2ID);

        // $tfa = new TwoFactorAuth(new BaconQrCodeProvider(4, '#ffffff', '#000000', 'svg'));
        $tfa = new TwoFactorAuth(new EndroidQrCodeProvider());
        $user_secret = $tfa->createSecret();
        $qr_code = $tfa->getQRCodeImageAsDataUri('Nobel Prizes', $user_secret);

        // Bind parameters to SQL
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", $pw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":2fa_code", $user_secret, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $reg_status = "Registracia prebehla uspesne.";
        } else {
            $reg_status = "Ups. Nieco sa pokazilo...";
        }

        unset($stmt);
    }
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
    <title>Registrácia</title>

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
            <h1>Registrácia</h1>
            <h2>Vytvorenie nového používateľského konta</h2>
        </hgroup>
    </header>
    <main>
        <?php if (isset($reg_status)) {
            echo "<h3>$reg_status</h3>";
        } ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <!-- TODO: Error messages should be separated for corresponding input and as in WEBTE1,
                       they must contain meaningful explanation, why the input is incorrect. Use a JS
                       framework. -->
            <label for="firstname">
                Meno:
                <input type="text" name="firstname" value="" id="firstname" placeholder="napr. John">
            </label>

            <label for="lastname">
                Priezvisko:
                <input type="text" name="lastname" value="" id="lastname" placeholder="napr. Doe">
            </label>

            <br>

            <label for="email">
                E-mail:
                <input type="email" name="email" value="" id="email" placeholder="napr. johndoe@example.com">
            </label>

            <label for="password">
                Heslo:
                <input type="password" name="password" value="" id="password">
            </label>

            <button type="submit">Vytvoriť konto</button>

            <?php
            if (!empty($errors)) {
                echo "<br><strong>Chyby:</strong><br>";
                echo "<div class='err'>";
                echo nl2br($errors);
                echo "</div>";
            }
            if (isset($qr_code)) {
                // If a QR code was generated after successful registration, display it.
                $message = '<p>Zadajte kód: ' . $user_secret . ' do aplikácie pre 2FA</p>';
                $message .= '<p>alebo naskenujte QR kód:<br><img src="' . $qr_code . '" alt="qr kod pre aplikaciu authenticator"></p>';
                echo $message;
                echo '<p>Teraz sa môžete prihlásiť: <a href="login.php">Login stránka</a></p>';
            }
            ?>

        </form>
        <p>Už máte vytvorené konto? <a href="login.php">Prihláste sa tu.</a></p>
    </main>
</body>

</html>