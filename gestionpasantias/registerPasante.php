<?php
// registerPasante.php
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$required = ['nombre', 'email', 'password', 'nota', 'horasTotales'];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }
}

$nombre = trim($data['nombre']);
$email = strtolower(trim($data['email']));
$password = $data['password'];
$nota = intval($data['nota']);
$horasTotales = intval($data['horasTotales']);

if ($nota < 0 || $nota > 20 || $horasTotales < 0) {
    echo json_encode(['success' => false, 'message' => 'Valores inválidos para nota u horas']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un usuario con ese email']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (nombre, email, password, role, nota, horas_totales, horas_cumplidas, asignado_empresa, ubicacion_empresa, comentarios) VALUES (?, ?, ?, 'pasante', ?, ?, 0, NULL, '', '')");
    $stmt->execute([$nombre, $email, $passwordHash, $nota, $horasTotales]);

    echo json_encode(['success' => true, 'message' => 'Pasante agregado correctamente']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
