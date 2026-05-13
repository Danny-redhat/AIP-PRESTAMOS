<?php require "../config/conexion.php";
$codigo = $_POST["codigo"];
$stmt = $pdo->prepare("
    SELECT * FROM prestamos
    WHERE codigo_confirmacion=? AND estado='prestado'
");
$stmt->execute([$codigo]);
$p = $stmt->fetch();
if ($p) {
    $pdo->prepare("
        UPDATE prestamos
        SET estado='devuelto', fecha_devolucion=NOW()
        WHERE id_prestamo=?
    ")->execute([$p["id_prestamo"]]);
    $pdo->prepare("
        UPDATE laptops SET estado='disponible'
        WHERE id=?
    ")->execute([$p["laptop_id"]]);
    echo "Laptop devuelta correctamente";
} else {
    echo "Código inválido";
}
?>