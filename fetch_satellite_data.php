<?php
$api_url = 'https://api.nasa.gov/planetary/smap?lat=0.3156&lon=32.5811&api_key=YOUR_API_KEY'; // Adjust lat/lon

$response = file_get_contents($api_url);
$data = json_decode($response, true);

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Data not available']);
}
?>
