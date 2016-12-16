<?php

function connect_db() {
    try {
        $db_username = "root";
        $db_password = "";
        $conn = new PDO('mysql:host=127.0.0.1;dbname=cleveland', $db_username, $db_password); //Change 127.0.0.1 to localhost
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}

//This should be ignored