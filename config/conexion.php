<?php
$host = "localhost";
$db   = "prestamos_db";
$user = "root";
$pass = "Mbasadre*25";
ry {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db;sslmode=require",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);        // ← más seguro contra SQL injection
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
