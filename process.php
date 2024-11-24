<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON format']);
        exit;
    }

    if ($input['action'] === 'add_book') {
        $stmt = $conn->prepare("INSERT INTO buku (kode_buku, judul_buku, kerusakan, kehilangan, urgensi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddd", $input['kode_buku'], $input['judul_buku'], $input['kerusakan'], $input['kehilangan'], $input['urgensi']);
        $stmt->execute();
        echo json_encode(['message' => 'Book added successfully']);
    }
}

if ($_GET['action'] === 'calculate_saw') {
    $result = $conn->query("SELECT * FROM buku");
    $buku = $result->fetch_all(MYSQLI_ASSOC);

    $maxC1 = max(array_column($buku, 'kerusakan'));
    $maxC2 = max(array_column($buku, 'kehilangan'));
    $maxC3 = max(array_column($buku, 'urgensi'));

    $finalData = [];
    foreach ($buku as $row) {
        $value = (0.4 * ($row['kerusakan'] / $maxC1)) +
                 (0.3 * ($row['kehilangan'] / $maxC2)) +
                 (0.3 * ($row['urgensi'] / $maxC3));

        // Berikan rekomendasi berdasarkan nilai SAW
        if ($value >= 0.8) {
            $explanation = "Rekomendasi Prioritas Tinggi: Buku ini memerlukan perhatian segera.";
        } elseif ($value >= 0.5) {
            $explanation = "Rekomendasi Prioritas Sedang: Buku ini cukup penting untuk diperbaiki.";
        } else {
            $explanation = "Rekomendasi Prioritas Rendah: Buku ini tidak terlalu mendesak.";
        }

        $finalData[] = [
            'kode_buku' => $row['kode_buku'],
            'judul_buku' => $row['judul_buku'],
            'saw_value' => round($value, 3),
            'explanation' => $explanation  // Tambahkan rekomendasi
        ];
    }

    usort($finalData, function ($a, $b) {
        return $b['saw_value'] <=> $a['saw_value'];
    });

    echo json_encode($finalData);
}
?>

