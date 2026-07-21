<?php
// superadmin/heatmap/kde_heatmap.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/kde_calculator.php';

// Matikan error HTML agar output murni JSON
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $params = $_GET;

    if (isset($params['minLat']) && isset($params['maxLat']) && 
        isset($params['minLng']) && isset($params['maxLng'])) {
        if ($params['minLat'] >= $params['maxLat'] || $params['minLng'] >= $params['maxLng']) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid bounds']);
            exit;
        }
    }

    $conn = getConnection();
    if (!$conn) {
        throw new Exception("Gagal terhubung ke database.");
    }

    $result = getKDEHeatmapData($conn, $params);
    $conn->close();

    echo json_encode($result);

} catch (\Throwable $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>