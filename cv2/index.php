<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login/register s 2FA</title>
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
            <h1>Demo - správa používateľov</h1>
            <h2>Registrácia a prihlásenie</h2>
        </hgroup>
    </header>
    <main>

        <?php

        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {
            // User is not logged in, show the link to login and registration form.
            echo '<p>Pre pokračovanie sa prosím <a href="login.php">prihláste</a> alebo sa <a href="register.php">zaregistrujte</a>.</p>';
        } else {
            // User is logged in, show a welcome message.
            echo '<h3>Vitaj ' . $_SESSION['fullname'] . ' </h3>';
            echo '<a href="restricted.php">Zabezpečená stránka</a>';
        }

        // TODO: This is just a demo. In the assignment, this should be only a part of the page, not the whole page.
        //       The page should contain a header with the login/register informations as shown here,
        //       a main section with table of nobel prize winners, etc...

        ?>
    </main>
</body>

</html>