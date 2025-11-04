<?php
/*
 * Fișier de configurare a bazei de date
 * -------------------------------------
 * Completează detaliile de mai jos cu datele tale de conectare la MySQL.
 */

// Detalii de conectare la baza de date
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '3dmarket');

// Crearea conexiunii mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Setarea setului de caractere la UTF-8
$conn->set_charset("utf8mb4");

?>
