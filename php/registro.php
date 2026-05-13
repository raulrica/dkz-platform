<?php
header('Content-Type: application/json');
require 'config.php';

$tipo      = $_POST['tipo']      ?? '';
$nombre    = trim($_POST['nombre']    ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email     = trim($_POST['email']     ?? '');
$telefono  = trim($_POST['telefono']  ?? '');
$password  = $_POST['password']  ?? '';

if (!$nombre || !$email || !$password || !$tipo) {
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

if ($tipo !== 'profesional' && $tipo !== 'cliente') {
    echo json_encode(['error' => 'Tipo de usuario no válido']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios 
        (nombre, apellidos, email, telefono, password, tipo) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellidos, $email, $telefono, $hash, $tipo]);
    $usuarioId = $pdo->lastInsertId();

    if ($tipo === 'profesional') {
        $categoria_id = $_POST['categoria_id'] ?? '';
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $skills       = trim($_POST['skills'] ?? '');
        $portfolioLink = trim($_POST['portfolio_link'] ?? '');
        $foto = null;

if (!empty($_FILES['foto']['name'])) {
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $extensionesPermitidas)) {
        echo json_encode(['error' => 'La foto debe ser jpg, jpeg, png o webp']);
        exit;
    }
    
    $nombreFoto = uniqid('foto_') . '.' . $ext;
    $rutaDestino = '../uploads/portfolio/' . $nombreFoto;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        $foto = $nombreFoto;
    }
}

        if (!$categoria_id || !is_numeric($categoria_id)) {
            echo json_encode(['error' => 'Debes seleccionar una categoría']);
            exit;
        }

        $stmt2 = $pdo->prepare("INSERT INTO profesionales 
            (usuario_id, categoria_id, descripcion, estado, foto) 
            VALUES (?, ?, ?, 'pendiente', ?)");
        $stmt2->execute([$usuarioId, $categoria_id, $descripcion, $foto]);
        $profesionalId = $pdo->lastInsertId();

        if ($skills) {
            $listaSkills = array_map('trim', explode(',', $skills));
            $stmtSkill = $pdo->prepare("INSERT INTO skills 
                (profesional_id, nombre) VALUES (?, ?)");
            foreach ($listaSkills as $skill) {
                if ($skill) $stmtSkill->execute([$profesionalId, $skill]);
            }
        }

        if ($portfolioLink) {
            $stmtP = $pdo->prepare("INSERT INTO portfolio 
                (profesional_id, tipo, valor) VALUES (?, 'link', ?)");
            $stmtP->execute([$profesionalId, $portfolioLink]);
        }

    } else {
        $necesidad = trim($_POST['necesidad'] ?? '');
        $stmt3 = $pdo->prepare("INSERT INTO clientes 
            (usuario_id, necesidad) VALUES (?, ?)");
        $stmt3->execute([$usuarioId, $necesidad]);
    }

    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['error' => 'Ese email ya está registrado']);
    } else {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>