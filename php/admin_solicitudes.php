<?php
header('Content-Type: application/json');
require 'config.php';

$accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';

try {
    if ($accion === 'listar') {
        $estado = $_GET['estado'] ?? 'pendiente';
        
        $estadosValidos = ['pendiente', 'aceptada', 'rechazada', 'completada'];
        if (!in_array($estado, $estadosValidos)) {
            echo json_encode(['error' => 'Estado no válido']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT s.id, s.descripcion, s.fecha_necesidad, s.estado, s.created_at,
                   u_cliente.nombre AS cliente_nombre, u_cliente.apellidos AS cliente_apellidos,
                   u_cliente.email AS cliente_email,
                   u_pro.nombre AS profesional_nombre, u_pro.apellidos AS profesional_apellidos,
                   c.nombre AS categoria
            FROM solicitudes s
            JOIN clientes cl ON cl.id = s.cliente_id
            JOIN usuarios u_cliente ON u_cliente.id = cl.usuario_id
            JOIN profesionales p ON p.id = s.profesional_id
            JOIN
            