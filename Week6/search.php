<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6 Live Search
// BIT3208 Advanced Web Design and Development
// File: search.php — AJAX search endpoint
// ============================================================
require_once 'includes/db_connect.php';

header('Content-Type: application/json');

$q    = trim($_GET['q'] ?? '');
$data = [];

if (strlen($q) >= 2) {
    $search = '%' . mysqli_real_escape_string($conn, $q) . '%';
    $result = mysqli_query($conn,
        "SELECT product_id, name, category, price, skin_type
         FROM products
         WHERE name LIKE '$search' OR category LIKE '$search' OR description LIKE '$search'
         ORDER BY rating DESC LIMIT 8"
    );

    $icons = [
        'Skincare'    => '🧴',
        'Makeup'      => '💄',
        'Haircare'    => '💆',
        'Perfumes'    => '🌸',
        'Accessories' => '🪞'
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'product_id' => $row['product_id'],
            'name'       => $row['name'],
            'category'   => $row['category'],
            'price'      => number_format($row['price']),
            'skin_type'  => $row['skin_type'],
            'icon'       => $icons[$row['category']] ?? '✨'
        ];
    }
}

echo json_encode($data);
?>