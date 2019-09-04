<?php
$json = json_decode(file_get_contents(__DIR__ . "/database.json"));

try {
    $db = new PDO("mysql:host=" . $json->host . ";dbname=" . $json->dbname . ";charset=" . $json->dbchar . ";", $json->dbuser, $json->dbpass);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>