<?php

require_once("config.php");

$db = connectDatabase($hostname, $database, $username, $password);

function processStatement($stmt) {
    if ($stmt->execute()) {
        return "Record inserted successfully.";
    } else {
        return "Error inserting record: " . implode(", ", $stmt->errorInfo());
    }
}

function insertLaureate($db, $name, $surname, $organisation, $sex, $birth_year, $death_year) {
    $stmt = $db->prepare("INSERT INTO laureates (fullname, organisation, sex, birth_year, death_year) VALUES (:fullname, :organisation, :sex, :birth_year, :death_year)");
    
    $fullname = $name . " " . $surname;
    
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
    $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
    $stmt->bindParam(':birth_year', $birth_year, PDO::PARAM_STR);
    $stmt->bindParam(':death_year', $death_year, PDO::PARAM_STR);

    return processStatement($stmt);
}

function insertCountry($db, $country_name) {
    $stmt = $db->prepare("INSERT INTO countries (country_name) VALUES (:country_name)");
    
    $stmt->bindParam(':country_name', $country_name, PDO::PARAM_STR);

    return processStatement($stmt);
}

function boundCountry($db, $laureate_id, $country_id) {
    $stmt = $db->prepare("INSERT INTO laureate_country (laureate_id, country_id) VALUES (:laureate_id, :country_id)");
    
    $stmt->bindParam(':laureate_id', $laureate_id, PDO::PARAM_INT);
    $stmt->bindParam(':country_id', $country_id, PDO::PARAM_INT);

    return processStatement($stmt);
}

function getLaureatesWithCountry($db) {
    $stmt = $db->prepare("
    SELECT laureates.fullname, laureates.sex, laureates.birth_year, laureates.death_year, countries.country_name 
    FROM laureates 
    LEFT JOIN laureate_country 
        INNER JOIN countries
        ON laureate_country.country_id = countries.id
    ON laureates.id = laureate_country.laureate_id");
    
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function insertLaureateWithCountry($db, $name, $surname, $organisation, $sex, $birth_year, $death_year, $country_name) {
    $db->beginTransaction();
    
    $status = insertLaureate($db, $name, $surname, $organisation, $sex, $birth_year, $death_year);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $laureate_id = $db->lastInsertId();
    
    $status = insertCountry($db, $country_name);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $country_id = $db->lastInsertId();
    
    $status = boundCountry($db, $laureate_id, $country_id);

    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }

    $db->commit();


    return $status;
}

// Example usage 

// $status = insertLaureate($db, "Peter", "Doe", NULL, "M", "1918", "1999");
// $status = insertCountry($db, "United Kingdom");
// $status = boundCountry($db, 3, 1);

// $status = insertLaureateWithCountry($db, "Susane", "Doe", NULL, "F", "1922", "1999", "Germany");
// $status = getLaureatesWithCountry($db);

// echo "<pre>";
// print_r($status);
// echo "</pre>";

