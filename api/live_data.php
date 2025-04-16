<?php
header('Content-Type: application/json');

// Simulating live data (Replace with API calls like Alpha Vantage, CoinGecko, etc.)
$data = [
    "stock_price" => rand(100, 200), // Simulated Apple Stock Price
    "crypto_price" => rand(30000, 40000), // Simulated Bitcoin Price
    "gold_price" => rand(1800, 2000), // Simulated Gold Price per ounce
    "indices" => [
        "nifty" => rand(23000, 25000),
        "sensex" => rand(75000, 78000),
        "banknifty" => rand(50000, 51000)
    ]
];

echo json_encode($data);
?>
