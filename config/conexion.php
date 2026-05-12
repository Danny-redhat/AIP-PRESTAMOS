<?php
$host = "localhost";
$db   = "prestamos_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,         PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // ← ya no necesitas FETCH_ASSOC en cada query
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);            // ← más seguro contra SQL injection
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}