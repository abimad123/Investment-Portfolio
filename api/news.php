<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Content-Type: application/json"); // Set response type to JSON

$apiKey = "425215ec8bdc496590009e38ebbe56a8"; // Your NewsAPI Key

// URLs for different news sources
$businessNewsUrl = "https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=$apiKey";
$appleNewsUrl = "https://newsapi.org/v2/everything?q=apple&from=2025-03-25&to=2025-03-25&sortBy=popularity&apiKey=$apiKey";

// Function to fetch data from an API
function fetchNews($url) {
    $contextOptions = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
        ]
    ];
    $context = stream_context_create($contextOptions);
    $response = file_get_contents($url, false, $context);
    if ($response === FALSE) {
        return [];
    }
    return json_decode($response, true)["articles"] ?? [];
}

// Fetch data from both APIs
$businessNews = fetchNews($businessNewsUrl);
$appleNews = fetchNews($appleNewsUrl);

// Merge news articles (limit total to 10 articles for performance)
$mergedNews = array_slice(array_merge($businessNews, $appleNews), 0, 10);

// Send the merged news as JSON response
echo json_encode(["status" => "ok", "articles" => $mergedNews]);
?>
