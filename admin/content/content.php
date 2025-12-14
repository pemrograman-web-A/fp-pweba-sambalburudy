<?php
// Konfigurasi Header untuk CORS dan JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT"); // Hanya GET (Read) dan PUT (Update) yang dibutuhkan
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Lokasi "Database" Konten
$dataFile = 'content.json';

// --- Fungsi Pembantu ---

function readContent() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        // Fallback jika file tidak ada
        return [
            "tagline_utama" => "Konten Default Tagline",
            "sub_tagline" => "Konten Default Subtagline",
            "deskripsi_khas" => "Konten Default Deskripsi",
            "teks_footer" => "Konten Default Footer"
        ];
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true);
}

function writeContent($content) {
    global $dataFile;
    $json = json_encode($content, JSON_PRETTY_PRINT);
    file_put_contents($dataFile, $json);
}

// --- Logika Utama CRUD (Read & Update) ---

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

$content = readContent();

switch ($method) {
    case 'GET':
        // READ: Mengambil semua konten
        echo json_encode($content);
        break;

    case 'PUT':
        // UPDATE: Mengubah salah satu atau beberapa item konten
        if (is_array($data)) {
            $updatedContent = array_merge($content, $data);
            writeContent($updatedContent);
            echo json_encode(['message' => 'Konten berhasil diperbarui.', 'data' => $data]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Data format tidak valid.']);
        }
        break;
        
    case 'POST': // Simulasi error jika mencoba POST
    case 'DELETE': // Simulasi error jika mencoba DELETE
        http_response_code(405);
        echo json_encode(['message' => 'Metode POST/DELETE tidak didukung untuk manajemen konten. Gunakan PUT untuk UPDATE.']);
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}
?>