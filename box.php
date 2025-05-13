<?php
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
  echo json_encode(['error' => 'Missing box ID']);
  exit;
}

$boxId = $_GET['id'];
$url = "https://api.opensensemap.org/boxes/$boxId";

$response = @file_get_contents($url);
if (!$response) {
  echo json_encode(['error' => 'Failed to fetch data from OpenSenseMap']);
  exit;
}

$data = json_decode($response, true);

if (!isset($data['name']) || !isset($data['currentLocation']['coordinates'])) {
  echo json_encode(['error' => 'Incomplete or invalid box data']);
  exit;
}

$lat = $data['currentLocation']['coordinates'][1];
$lng = $data['currentLocation']['coordinates'][0];
$boxName = $data['name'];

$sensors = [];

foreach ($data['sensors'] as $sensor) {
  $title = $sensor['title'];
  $unit = $sensor['unit'] ?? '';
  $value = isset($sensor['lastMeasurement']['value']) ? floatval($sensor['lastMeasurement']['value']) : null;

  $sensors[] = [
    'title' => $title,
    'unit' => $unit,
    'value' => $value
  ];
}

echo json_encode([
  'boxName' => $boxName,
  'lat' => $lat,
  'lng' => $lng,
  'sensors' => $sensors
]);
