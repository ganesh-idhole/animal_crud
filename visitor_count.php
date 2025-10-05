<?php
session_start();

// Get visitor IP address
$ip = $_SERVER['REMOTE_ADDR'];
$file = 'visitors.json';

// Load existing visitor data
$visitors = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Check if this IP was already counted
if (!in_array($ip, $visitors)) {
    // Add new unique visitor
    $visitors[] = $ip;
    file_put_contents($file, json_encode($visitors));
}

// Count unique visitors
$total_unique = count($visitors);
?>

<p>Unique Visitors: <?= $total_unique ?></p>
