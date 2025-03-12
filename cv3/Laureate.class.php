<?php

class Laureate {

    private $db;
    // TODO: Implement class field according to the database table.

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all records
    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM laureates");
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one record
    public function show($id) {
        // TODO: Implement Where caluse by fullname, organisation...
        $stmt = $this->db->prepare("SELECT * FROM laureates WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new record
    public function store($gender, $birth, $death, $fullname = null, $organisation = null) {
        $stmt = $this->db->prepare("INSERT INTO laureates (fullname, organisation, gender, birth, death) VALUES (:fullname, :organisation, :gender, :birth, :death)");
        
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':birth', $birth, PDO::PARAM_INT);
        $stmt->bindParam(':death', $death, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return $this->db->lastInsertId();
    }

    // Update a record
    public function update($id, $gender, $birth, $death, $fullname = null, $organisation = null) {
        $stmt = $this->db->prepare("UPDATE laureates SET fullname = :fullname, organisation = :organisation, gender = :gender, birth = :birth, death = :death WHERE id = :id");

        // TODO: Where clause by fullname or organisation

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':birth', $birth, PDO::PARAM_INT);
        $stmt->bindParam(':death', $death, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
        return 0;    
    }

    // Delete a record
    public function destroy($id) {
        // TODO: Check if there are any prizes only for this laureate, if so, delete them too
        // TODO: Check if exist
        $stmt = $this->db->prepare("DELETE FROM laureates WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return 0;
    }

    // TODO: Implement method inserting more than one laureate.
    // TODO: Implement method for inserting laureate with Prize.
}