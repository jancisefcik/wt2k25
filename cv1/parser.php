<?php

// Simple CSV file parser
function parseCSV($filename) {
    $handle = fopen($filename, "r");
    $data = array();
    while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
        $data[] = array_filter($row);  // push only non-empty values
    }
    fclose($handle);
    
    unset($data[0]);  // remove the first row (column names)
    return $data;
}

$parsed_data = parseCSV("filename_to_parse.csv");

echo "<pre>";
print_r($parsed_data);
echo "</pre>";

// It is necessary to check for an exception when inserting, that the record already exists.
// If the record exists, e.g., country or price, the existing country or price must be retrieved and
// its identifier must be inserted into the mapping table.
