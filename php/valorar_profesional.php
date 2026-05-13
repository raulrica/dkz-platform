<?php
header('Content-Type: application/json');
require 'config.php';

$profesional_id = $_POST['profesional_id'] ?? '';
$puntuacion     = $_POST['puntuacion']     ?? '';
$comentario     = trim($_POST['comentario']     ?? '');
$administrador  = trim($_POST['administrador']  ?? '');

if (!$profesional_id || !is_numeric($profesional_id)) {
    echo json_encode(['error' => 'ID de profesional no válido']);
    exit;
}

if (!$puntuacion || !is_numeric($puntuacion) || $puntuacion < 1 || $puntuacion > 5) {
    echo json_encode(['error' => 'La puntuación debe ser entre 1 y 5']);
    exit;
}

if (!$administrador) {
    echo json_encode(['error' => 'El administrador es obligatorio']);
    exit;
}

try {
    // 1. Guardar la valoración
    $stmt = $pdo->prepare("INSERT INTO valoraciones 
        (profesional_id, puntuacion, comentario, administrador) 
        VALUES (?, ?, ?, ?)");
    $stmt->execute([$profesional_id, $puntuacion, $comentario, $administrador]);

    // 2. Recalcular la media y actualizar profesionales
    $stmtMedia = $pdo->prepare("
        UPDATE profesionales 
        SET valoracion_media = (
            SELECT ROUND(AVG(puntuacion), 1) 
            FROM valoraciones 
            WHERE profesional_id = ?
        )
        WHERE id = ?
    ");
    $stmtMedia->execute([$profesional_id, $profesional_id]);

    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>