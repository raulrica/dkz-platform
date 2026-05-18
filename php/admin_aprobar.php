<?php
header('Content-Type: application/json');
require 'config.php';

$profesional_id = $_POST['profesional_id'] ?? '';
$estado         = $_POST['estado']         ?? '';
$administrador  = trim($_POST['administrador'] ?? '');

if (!$profesional_id || !is_numeric($profesional_id)) {
    echo json_encode(['error' => 'ID de profesional no válido']);
    exit;
}

if ($estado !== 'aprobado' && $estado !== 'rechazado') {
    echo json_encode(['error' => 'Estado no válido']);
    exit;
}

if (!$administrador) {
    echo json_encode(['error' => 'El administrador es obligatorio']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE profesionales 
        SET estado = ?
        WHERE id = ?
    ");
    $stmt->execute([$estado, $profesional_id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'Profesional no encontrado']);
        exit;
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Profesional ' . $estado . ' correctamente']);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
