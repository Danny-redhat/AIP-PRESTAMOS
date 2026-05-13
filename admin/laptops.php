<?php session_start(); require "../config/conexion.php";
if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}
$laptops = $pdo->query("SELECT * FROM laptops")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Laptops</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="contenedor">
    <h2>Inventario de Laptops</h2>
    <a href="laptops_agregar.php"><button class="btn-success">➕ Agregar Laptop</button></a>
    <table border="1" cellpadding="5">
        <tr style="background-color:#003366; color:#FFD700;">
            <th>Código</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php if(count($laptops) > 0): ?>
            <?php foreach($laptops as $l): ?>
                <tr>
                    <td><?= htmlspecialchars($l['codigo']) ?></td>
                    <td><?= htmlspecialchars($l['marca']) ?></td>
                    <td><?= htmlspecialchars($l['modelo']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($l['tipo'])) ?></td>
                    <td><?= ucfirst(htmlspecialchars($l['estado'])) ?></td>
                    <td>
                        <a href="laptops_editar.php?id=<?= $l['id'] ?>"><button class="btn-success">Editar</button></a>
                        <a href="laptops_eliminar.php?id=<?= $l['id'] ?>" onclick="return confirm('¿Eliminar laptop?')"><button class="btn-danger">Eliminar</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No hay laptops registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>