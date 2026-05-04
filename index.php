<?php
session_start();
require "config/conexion.php";

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sistema de Préstamos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container">
    <h2>🏫 Sistema de Préstamos del Colegio</h2>
    <p style="text-align:center;">Seleccione una opción del menú superior</p>
</div>

</body>
</html>

<?php if($_SESSION['rol']=='administrador'): ?>
    <a href="admin/usuarios.php"><button>Usuarios</button></a>
    <a href="admin/laptops.php"><button>Laptops</button></a>
    <a href="admin/incidencias_listar.php"><button>Incidencias</button></a>
    <a href="admin/solicitudes_listar.php"><button>Solicitudes</button></a>
    <a href="admin/historial.php"><button>Historial</button></a>
<?php else: ?>
    <a href="profesor/incidencias.php"><button>Reportar Incidencia</button></a>
    <a href="profesor/solicitudes.php"><button>Solicitar Laptop</button></a>
    <a href="profesor/historial.php"><button>Historial</button></a>
<?php endif; ?>

<a href="config/logout.php"><button>Cerrar Sesión</button></a>