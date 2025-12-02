<?php

// Read input JSON file
$jsonFile = "01-12.json";
$json = file_get_contents($jsonFile);

// Decode
$data = json_decode($json, true);

// State-wise result
$result = [];

foreach ($data as $item) {

    $state  = trim($item["state"]);
    $device = trim($item["devicecode"]);

    // Skip empty devicecodes
    if ($device == "") continue;

    // Init state if not exists
    if (!isset($result[$state])) {
        $result[$state] = [
            "devices" => []
        ];
    }

    // Unique devicecode (use as array key)
    $result[$state]["devices"][$device] = true;
}

// Convert keys to normal array and add count
foreach ($result as $state => $info) {
    $deviceList = array_keys($info["devices"]);
    $result[$state] = [
        "count" => count($deviceList),
        "devices" => $deviceList
    ];
}

// ------------------------------------------------------
// 🔽 NOW DOWNLOAD AS JSON FILE
// ------------------------------------------------------

$outputFile = "state_device_summary.json";
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $outputFile . '"');

echo json_encode($result, JSON_PRETTY_PRINT);

exit;
?>
