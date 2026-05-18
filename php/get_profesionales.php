<?php
header('Content-Type: application/json');
require 'config.php';

$categoria_id = $_GET['categoria_id'] ?? null;

try {
    if ($categoria_id && is_numeric($categoria_id)) {
        $stmt = $pdo->prepare("
            SELECT u.nombre, u.apellidos, p.id, p.descripcion, 
                   p.valoracion_media, p.foto, c.nombre AS categoria
            FROM profesionales p
            JOIN usuarios u ON u.id = p.usuario_id
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.estado = 'aprobado'
            AND p.categoria_id = ?
            ORDER BY p.valoracion_media DESC
        ");
        $stmt->execute([$categoria_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT u.nombre, u.apellidos, p.id, p.descripcion, 
                   p.valoracion_media, p.foto, c.nombre AS categoria
            FROM profesionales p
            JOIN usuarios u ON u.id = p.usuario_id
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.estado = 'aprobado'
            ORDER BY p.valoracion_media DESC
        ");
        $stmt->execute();
    }

    $profesionales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'datos' => $profesionales]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>