<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAuth();
checkRole(['super_admin']);

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit();
}

$id = intval($_GET['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit();
}

$conn = getConnection();
$stmt = $conn->prepare("SELECT id, username, nama, no_hp, role, bpk_id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}

$stmt->close();
$conn->close();
?>