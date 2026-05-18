<?php
header('Content-Type: application/json');
require 'config.php';

$estado = $_GET['estado'] ?? 'pendiente';

$estadosValidos = ['pendiente', 'aprobado', 'rechazado'];
if (!in_array($estado, $estadosValidos)) {
    echo json_encode(['error' => 'Estado no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.descripcion, p.estado, p.foto,
               p.valoracion_media, p.created_at,
               u.nombre, u.apellidos, u.email, u.telefono,
               c.nombre AS categoria
        FROM profesionales p
        JOIN usuarios u ON u.id = p.usuario_id
        JOIN categorias c ON c.id = p.categoria_id
        WHERE p.estado = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$estado]);
    $profesionales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['ok' => true, 'datos' => $profesionales]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>