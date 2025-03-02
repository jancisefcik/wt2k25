<?php

session_start();

// Release session variables. These two commands are equivalent.
$_SESSION = array();
session_unset();

// Delete the session.
session_destroy();

// Redirect to the main page.
header("location: index.php");
exit;