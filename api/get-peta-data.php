<?php
// api/get-peta-data.php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = getConnection();

$kecamatan = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : '';
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$response = [
    'kebakaran' => [],
    'hydrant' => [],
    'bpk' => [],
    'stats' => []
];

// Build query untuk kebakaran
$sql = "SELECT * FROM kejadian_kebakaran WHERE 1=1";
$params = [];
$types = "";

if ($kecamatan) {
    $sql .= " AND kecamatan = ?";
    $params[] = $kecamatan;
    $types .= "s";
}

if ($date_from) {
    $sql .= " AND DATE(waktu) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if ($date_to) {
    $sql .= " AND DATE(waktu) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$sql .= " ORDER BY waktu DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$kebakaran = [];
while ($row = $result->fetch_assoc()) {
    $kebakaran[] = $row;
}
$response['kebakaran'] = $kebakaran;

// Hydrant
if ($tipe == 'all' || $tipe == 'hydrant') {
    $sqlHydrant = "SELECT * FROM hydrant";
    if ($kecamatan) {
        $sqlHydrant .= " WHERE kecamatan = '$kecamatan'";
    }
    $hydrantResult = $conn->query($sqlHydrant);
    while ($row = $hydrantResult->fetch_assoc()) {
        $response['hydrant'][] = $row;
    }
}

// BPK
if ($tipe == 'all' || $tipe == 'bpk') {
    $sqlBpk = "SELECT * FROM bpk";
    if ($kecamatan) {
        $sqlBpk .= " WHERE kecamatan = '$kecamatan'";
    }
    $bpkResult = $conn->query($sqlBpk);
    while ($row = $bpkResult->fetch_assoc()) {
        $response['bpk'][] = $row;
    }
}

// Stats
$response['stats'] = [
    'total_kejadian' => count($kebakaran),
    'total_luka' => array_sum(array_column($kebakaran, 'korban_luka')),
    'total_jiwa' => array_sum(array_column($kebakaran, 'korban_jiwa')),
    'total_bangunan' => array_sum(array_column($kebakaran, 'jumlah_bangunan'))
];

$conn->close();
echo json_encode($response);
?>