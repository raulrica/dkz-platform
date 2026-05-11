<?php
header('Content-Type: application/json');
require 'config.php';

$profesional_id = $_GET['id'] ?? null;

if (!$profesional_id || !is_numeric($profesional_id)) {
    echo json_encode(['error' => 'ID de profesional no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT u.nombre, u.apellidos, u.email, u.telefono,
               p.id, p.descripcion, p.valoracion_media,
               c.nombre AS categoria
        FROM profesionales p
        JOIN usuarios u ON u.id = p.usuario_id
        JOIN categorias c ON c.id = p.categoria_id
        WHERE p.id = ?
        AND p.estado = 'aprobado'
    ");
    $stmt->execute([$profesional_id]);
    $profesional = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profesional) {
        echo json_encode(['error' => 'Profesional no encontrado']);
        exit;
    }

    $stmtSkills = $pdo->prepare("
        SELECT nombre FROM skills 
        WHERE profesional_id = ?
    ");
    $stmtSkills->execute([$profesional_id]);
    $skills = $stmtSkills->fetchAll(PDO::FETCH_COLUMN);

    $stmtPortfolio = $pdo->prepare("
        SELECT tipo, valor FROM portfolio 
        WHERE profesional_id = ?
    ");
    $stmtPortfolio->execute([$profesional_id]);
    $portfolio = $stmtPortfolio->fetchAll(PDO::FETCH_ASSOC);

    $profesional['skills']    = $skills;
    $profesional['portfolio'] = $portfolio;

    echo json_encode(['ok' => true, 'datos' => $profesional]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>