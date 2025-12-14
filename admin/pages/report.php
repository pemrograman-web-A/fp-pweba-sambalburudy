<?php
// /admin/api/report/report_api.php

// KOREKSI PATH: Naik dua tingkat (../../) dari /api/report/ ke /admin/config/
require_once '../../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Internal Server Error: Database Connection Failed."]);
    exit();
}

// --- Fungsi Pembantu ---

function getFilterDates($period, $conn) {
    // Fungsi ini menentukan tanggal mulai dan akhir berdasarkan parameter 'period'
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
            default: // Default 7 hari
                $startDate = (new DateTime('today - 7 days'))->format('Y-m-d');
                $periodDisplay = "7 Hari Terakhir";
                break;
        }
        return ['start' => $startDate, 'end' => $endDate, 'display' => $periodDisplay];
    } catch (Exception $e) {
        // Fallback jika DateTime error
        return ['start' => date('Y-m-d', strtotime('-7 days')), 'end' => date('Y-m-d'), 'display' => '7 Hari Terakhir'];
    }
}

// --- Logika Utama ---

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $period = $_GET['period'] ?? '7_days';
    $dates = getFilterDates($period, $conn);
    $startDate = $dates['start'];
    $endDate = $dates['end'];

    // 1. Ambil Ringkasan Metrik
    // PASTIKAN status 'COMPLETED' di sini sesuai dengan status transaksi selesai di database Anda
    $sqlSummary = "SELECT 
        COUNT(transaction_id) AS total_transactions,
        SUM(total_amount) AS total_revenue,
        SUM(total_items) AS total_items
    FROM transactions
    WHERE status = 'COMPLETED' AND transaction_date BETWEEN ? AND ?";
    
    $summaryData = ['totalTransactions' => 0, 'totalRevenue' => 0, 'totalItems' => 0];

    if ($stmt = $conn->prepare($sqlSummary)) {
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            $summaryData['totalTransactions'] = (int)($row['total_transactions'] ?? 0);
            $summaryData['totalRevenue'] = (int)($row['total_revenue'] ?? 0);
            $summaryData['totalItems'] = (int)($row['total_items'] ?? 0);
        }
        $stmt->close();
    }

    // 2. Ambil Detail Transaksi (Tabel)
    $sqlDetails = "SELECT 
        transaction_id AS id, 
        transaction_date AS date, 
        total_amount AS amount, 
        total_items AS items
    FROM transactions
    WHERE status = 'COMPLETED' AND transaction_date BETWEEN ? AND ?
    ORDER BY transaction_date DESC";
    
    $transactions = [];

    if ($stmt = $conn->prepare($sqlDetails)) {
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $row['amount'] = (int)$row['amount']; 
            $row['items'] = (int)$row['items'];
            $transactions[] = $row;
        }
        $stmt->close();
    }

    // 3. Gabungkan dan kirim response
    http_response_code(200);
    echo json_encode([
        'period_info' => ['start' => $startDate, 'end' => $endDate, 'period' => $dates['display']],
        'totalRevenue' => $summaryData['totalRevenue'],
        'totalTransactions' => $summaryData['totalTransactions'],
        'totalItems' => $summaryData['totalItems'],
        'transactions' => $transactions
    ]);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Metode tidak diizinkan.']);
}

$conn->close();
?>