<?php
// Utility functions for user registration.
// Functions for checking empty fields, length, username, email and user existence.

function isEmpty($field) {
    // Function to check if the variable is empty after trimming white spaces.
    // The trim() method trims and removes spaces, tabs, and other "whitespaces".
    if (empty(trim($field))) {
        return true;
    }
    return false;
}

function userExist($db, $email) {
    // Function to check if a user with the given "email" exists.
    $exist = false;

    $param_email = trim($email);

    $sql = "SELECT id FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $exist = true;
    }

    unset($stmt);

    return $exist;
}