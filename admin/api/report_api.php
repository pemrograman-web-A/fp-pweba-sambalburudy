<?php

ini_set('display_errors', 0); 
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

$configPath = '../config/config.php';

if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(["message" => "Server Error: Config file not found at " . $configPath]);
    exit();
}

require_once $configPath; 

// Cek Koneksi Database
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database Connection Failed: " . ($conn->connect_error ?? "Conn object missing")]);
    exit();
}

function getFilterDates($period) {
    try {
        $now = new DateTime('today');
        $startDate = '';
        $endDate = $now->format('Y-m-d');
        $periodDisplay = '';

        switch ($period) {
            case '7_days':
                $startDate = (new DateTime('today - 7 days'))->format('Y-m-d');
                $periodDisplay = "7 Hari Terakhir";
                break;
            case 'this_month':
                $startDate = (new DateTime('first day of this month'))->format('Y-m-d');
                $periodDisplay = "Bulan Ini";
                break;
            case 'last_month':
                $startDate = (new DateTime('first day of last month'))->format('Y-m-d');
                $endDate = (new DateTime('last day of last month'))->format('Y-m-d');
                $periodDisplay = "Bulan Lalu";
                break;
            case 'this_year':
                $startDate = (new DateTime('first day of January ' . date('Y')))->format('Y-m-d');
                $periodDisplay = "Tahun Ini";
                break;
            default: 
                $startDate = (new DateTime('today - 7 days'))->format('Y-m-d');
                $periodDisplay = "7 Hari Terakhir";
                break;
        }
        return ['start' => $startDate, 'end' => $endDate, 'display' => $periodDisplay];
    } catch (Exception $e) {
        return ['start' => date('Y-m-d', strtotime('-7 days')), 'end' => date('Y-m-d'), 'display' => '7 Hari Terakhir'];
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $period = $_GET['period'] ?? '7_days';
        $dates = getFilterDates($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // --- 1. RINGKASAN DATA ---
        // Pastikan nama tabel 'transactions' sesuai dengan database Anda!
        $sqlSummary = "SELECT 
            COALESCE(COUNT(transaction_id), 0) AS total_transactions,
            COALESCE(SUM(total_amount), 0) AS total_revenue,
            COALESCE(SUM(total_items), 0) AS total_items
        FROM transactions
        WHERE status = 'COMPLETED' AND DATE(transaction_date) BETWEEN ? AND ?";
        
        $summaryData = ['totalTransactions' => 0, 'totalRevenue' => 0, 'totalItems' => 0];

        if ($stmt = $conn->prepare($sqlSummary)) {
            $stmt->bind_param("ss", $startDate, $endDate);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($row) {
                    $summaryData['totalTransactions'] = (int)$row['total_transactions'];
                    $summaryData['totalRevenue'] = (float)$row['total_revenue'];
                    $summaryData['totalItems'] = (int)$row['total_items'];
                }
            } else {
                throw new Exception("SQL Error (Summary): " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("SQL Prepare Error (Summary): " . $conn->error);
        }

        // --- 2. DETAIL TRANSAKSI ---
        $sqlDetails = "SELECT 
            transaction_id AS id, 
            transaction_date AS date, 
            total_amount AS amount, 
            total_items AS items
        FROM transactions
        WHERE status = 'COMPLETED' AND DATE(transaction_date) BETWEEN ? AND ?
        ORDER BY transaction_date DESC";
        
        $transactions = [];

        if ($stmt = $conn->prepare($sqlDetails)) {
            $stmt->bind_param("ss", $startDate, $endDate);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $row['amount'] = (float)$row['amount'];
                    $row['items'] = (int)$row['items'];
                    $transactions[] = $row;
                }
            } else {
                throw new Exception("SQL Error (Details): " . $stmt->error);
            }
            $stmt->close();
        }

        echo json_encode([
            'period_info' => ['start' => $startDate, 'end' => $endDate, 'period' => $dates['display']],
            'totalRevenue' => $summaryData['totalRevenue'],
            'totalTransactions' => $summaryData['totalTransactions'],
            'totalItems' => $summaryData['totalItems'],
            'transactions' => $transactions
        ]);

    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    // Kirim pesan error JSON yang valid
    echo json_encode(['message' => 'Server Error: ' . $e->getMessage()]);
}

if(isset($conn)) $conn->close();
?>