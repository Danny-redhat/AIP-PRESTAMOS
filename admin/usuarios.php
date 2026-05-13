<?php session_start(); require "../config/conexion.php";
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}
$usuarios = $pdo->query("
    SELECT * FROM usuarios 
    WHERE rol = 'profesor'
    ORDER BY nombre_completo
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Profesores</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="contenedor">
    <h2>Lista de Profesores</h2>
    <a href="usuarios_agregar.php">
        <button class="btn-success">➕ Agregar Profesor</button>
    </a>
    <br><br>
    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Área</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['dni']) ?></td>
                <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= htmlspecialchars($u['telefono']) ?></td>
                <td><?= htmlspecialchars($u['area']) ?></td>
                <td>
                    <a href="usuarios_editar.php?id=<?= $u['id_usuario'] ?>">
                        <button>Editar</button>
                    </a>
                    <a href="usuarios_eliminar.php?id=<?= $u['id_usuario'] ?>"
                       onclick="return confirm('¿Eliminar profesor?')">
                        <button class="btn-danger">Eliminar</button>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>