<?php
header('Content-Type: application/json');
require 'config.php';

$cliente_id      = $_POST['cliente_id']      ?? '';
$profesional_id  = $_POST['profesional_id']  ?? '';
$descripcion     = trim($_POST['descripcion']     ?? '');
$fecha_necesidad = $_POST['fecha_necesidad'] ?? null;

if (!$cliente_id || !is_numeric($cliente_id)) {
    echo json_encode(['error' => 'ID de cliente no válido']);
    exit;
}

if (!$profesional_id || !is_numeric($profesional_id)) {
    echo json_encode(['error' => 'ID de profesional no válido']);
    exit;
}

if (!$descripcion) {
    echo json_encode(['error' => 'La descripción es obligatoria']);
    exit;
}

try {
    // Comprobar que el profesional está aprobado
    $stmtCheck = $pdo->prepare("
        SELECT id FROM profesionales 
        WHERE id = ? AND estado = 'aprobado'
    ");
    $stmtCheck->execute([$profesional_id]);
    
    if (!$stmtCheck->fetch()) {
        echo json_encode(['error' => 'Este profesional no está disponible']);
        exit;
    }

    // Guardar la solicitud
    $stmt = $pdo->prepare("INSERT INTO solicitudes 
        (cliente_id, profesional_id, descripcion, fecha_necesidad) 
        VALUES (?, ?, ?, ?)");
    $stmt->execute([$cliente_id, $profesional_id, $descripcion, $fecha_necesidad]);

    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>